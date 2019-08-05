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
use TIG\Vendiro\Model\Payment\Vendiro;
use TIG\Vendiro\Service\Order\Create\CartManager;

class Create
{
    /** @var CartManager */
    private $cart;

    /** @var Product */
    private $product;

    /** @var DataObjectFactory */
    private $dataObjectFactory;

    /** @var Log */
    private $logger;

    /**
     * @param CartManager       $cart
     * @param Product           $product
     * @param DataObjectFactory $dataObjectFactory
     * @param Log               $logger
     */
    public function __construct(
        CartManager $cart,
        Product $product,
        DataObjectFactory $dataObjectFactory,
        Log $logger
    ) {
        $this->cart = $cart;
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

        $this->cart->addAddress($vendiroOrder['invoice_address'], 'Billing');
        $this->cart->addAddress($vendiroOrder['delivery_address'], 'Shipping');

        $shippingCost = $vendiroOrder['shipping_cost'] + $vendiroOrder['administration_cost'];
        $this->cart->setShippingMethod('tig_vendiro_shipping', $shippingCost);
        $this->cart->setPaymentMethod(Vendiro::PAYMENT_CODE);

        return $this->placeOrder();
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
}
