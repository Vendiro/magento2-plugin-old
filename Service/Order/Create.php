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
use TIG\Vendiro\Exception as VendiroException;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\Carrier\Vendiro as VendiroCarrier;
use TIG\Vendiro\Model\Payment\Vendiro as VendiroPayment;
use TIG\Vendiro\Service\Order\Create\CartManager;

use \Magento\Framework\Session\SessionManagerInterface as CoreSession;

class Create
{
    /** @var CartManager */
    private $cart;

    /** @var OrderStatusManager */
    private $orderStatusManager;

    /** @var Product */
    private $product;

    /** @var DataObjectFactory */
    private $dataObjectFactory;

    /** @var Log */
    private $logger;

    /**
     * @var CoreSession
     */
    protected $_coreSession;

    /**
     * @param CartManager          $cart
     * @param OrderStatusManager  $orderStatusManager
     * @param Product              $product
     * @param DataObjectFactory    $dataObjectFactory
     * @param Log                  $logger
     * @param CoreSession          $coreSession
     */
    public function __construct(
        CartManager $cart,
        OrderStatusManager $orderStatusManager,
        Product $product,
        DataObjectFactory $dataObjectFactory,
        Log $logger,
        CoreSession $coreSession
    ) {
        $this->cart = $cart;
        $this->orderStatusManager = $orderStatusManager;
        $this->product = $product;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->logger = $logger;
        $this->_coreSession = $coreSession;
    }

    /**
     * @return CoreSession
     */
    public function getCoreSession()
    {
        return $this->_coreSession;
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

        if ($this->getCoreSession()->getFulfilmentByMarketplace() == true) {
            throw new VendiroException(
                __('Fulfilment by marketplace flag already set, this means another order is busy.')
            );
        }

        if (isset($vendiroOrder['fulfilment_by_marketplace']) && $vendiroOrder['fulfilment_by_marketplace'] == 'true') {
            $this->getCoreSession()->setFulfilmentByMarketplace(true);
        }

        $newOrderId = $this->placeOrder();

        $this->getCoreSession()->unsFulfilmentByMarketplace();

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
        $this->cart->setShippingMethod(VendiroCarrier::SHIPPING_CARRIER_METHOD, $shippingCost);
        $this->cart->setPaymentMethod(VendiroPayment::PAYMENT_CODE);
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

        if (isset($vendiroOrder['fulfilment_by_marketplace']) && $vendiroOrder['fulfilment_by_marketplace'] == 'true') {
            $comment .= "<br/>Fulfilment by marketplace: true";
        }

        $this->orderStatusManager->addHistoryComment($magentoOrderId, $comment);
    }
}
