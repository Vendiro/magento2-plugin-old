<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Vendiro\Service\Order;

use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Sales\Model\Order;
use TIG\Vendiro\Exception as VendiroException;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\Payment\Vendiro;
use TIG\Vendiro\Service\Order\Create\CartManager;

class Create
{
    /** @var CartManager */
    private $cart;

    /** @var MagentoStatusManager */
    private $magentoStatusManager;

    /** @var Product */
    private $product;

    /** @var DataObjectFactory */
    private $dataObjectFactory;

    /** @var Log */
    private $logger;

    /**
     * @param CartManager          $cart
     * @param MagentoStatusManager $magentoStatusManager
     * @param Product              $product
     * @param DataObjectFactory    $dataObjectFactory
     * @param Log                  $logger
     */
    public function __construct(
        CartManager $cart,
        MagentoStatusManager $magentoStatusManager,
        Product $product,
        DataObjectFactory $dataObjectFactory,
        Log $logger
    ) {
        $this->cart = $cart;
        $this->magentoStatusManager = $magentoStatusManager;
        $this->product = $product;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->logger = $logger;
    }

    /**
     * @param $vendiroOrder
     *
     * @return int
     * @throws VendiroException
     */
    public function execute($vendiroOrder)
    {
        $storeCode = $vendiroOrder['marketplace']['reference'];
        $this->cart->createCart($storeCode);

        foreach ($vendiroOrder['orderlines'] as $apiProduct) {
            $this->addProducts($apiProduct, $storeCode);
        }

        $this->addAddresses($vendiroOrder['invoice_address'], $vendiroOrder['delivery_address']);

        $shippingCost = $vendiroOrder['shipping_cost'] + $vendiroOrder['administration_cost'];
        $this->setMethods($shippingCost);

        $newOrderId = $this->placeOrder();

        if ($newOrderId) {
            $this->updateOrderCommentAndStatus($newOrderId, $vendiroOrder);
        }

        return $newOrderId;
    }

    /**
     * @param $apiProduct
     * @param $storeId
     *
     * @throws VendiroException
     */
    private function addProducts($apiProduct, $storeCode = null)
    {
        $data = [
            'qty' => (int)$apiProduct['amount'],
            'custom_price' => $apiProduct['value'],
        ];

        $quoteProductData = $this->dataObjectFactory->create($data);

        $product = $this->product->getBySku($apiProduct['sku'], $storeCode);

        $this->cart->addProduct($product, $quoteProductData);
    }

    /**
     * @param array $billingAddress
     * @param array $shippingAddress
     */
    private function addAddresses($billingAddress, $shippingAddress)
    {
        $this->cart->addAddress($billingAddress, 'Billing');
        $this->cart->addAddress($shippingAddress, 'Shipping');
    }

    /**
     * @param int|float|string $shippingCost
     */
    private function setMethods($shippingCost)
    {
        $this->cart->setShippingMethod('tig_vendiro_shipping', $shippingCost);
        $this->cart->setPaymentMethod(Vendiro::PAYMENT_CODE);
    }

    /**
     * @return bool|int
     */
    private function placeOrder()
    {
        $newOrderId = false;

        try {
            $newOrderId = $this->cart->placeOrder();
        } catch (\Exception $exception) {
            $this->logger->critical('Vendiro import went wrong: ' . $exception->getMessage());
        }

        return $newOrderId;
    }

    /**
     * @param int $magentoOrderId
     * @param     $vendiroOrder
     */
    private function updateOrderCommentAndStatus($magentoOrderId, $vendiroOrder)
    {
        $comment = "Order via Vendiro<br>" .
            "Marketplace: " . $vendiroOrder['marketplace']['name'] . "<br/>" .
            $vendiroOrder['marketplace']['name'] . " ID: " . $vendiroOrder['marketplace_order_id'];

        $this->magentoStatusManager->addHistoryComment($magentoOrderId, $comment);

        if ($vendiroOrder['fulfilment_by_marketplace'] == 'true') {
            $this->magentoStatusManager->setNewState($magentoOrderId, Order::STATE_COMPLETE);
        }
    }
}
