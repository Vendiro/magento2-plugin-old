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

use Magento\Framework\Session\SessionManagerInterface as CoreSession;
use TIG\Vendiro\Exception as VendiroException;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\Carrier\Vendiro as VendiroCarrier;
use TIG\Vendiro\Model\Payment\Vendiro as VendiroPayment;
use TIG\Vendiro\Service\Order\Create\CartManager;

class Create
{
    /** @var CartManager */
    private $cart;

    /** @var OrderStatusManager */
    private $orderStatusManager;

    /** @var Product */
    private $product;

    /** @var Log */
    private $logger;

    /** @var CoreSession */
    private $coreSession;

    /**
     * @param CartManager        $cart
     * @param OrderStatusManager $orderStatusManager
     * @param Product            $product
     * @param Log                $logger
     * @param CoreSession        $coreSession
     */
    public function __construct(
        CartManager $cart,
        OrderStatusManager $orderStatusManager,
        Product $product,
        Log $logger,
        CoreSession $coreSession
    ) {
        $this->cart = $cart;
        $this->orderStatusManager = $orderStatusManager;
        $this->product = $product;
        $this->logger = $logger;
        $this->coreSession = $coreSession;
    }

    /**
     * @param $vendiroOrder
     *
     * @return int|string
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

        $newOrderId = $this->prepareAndPlaceOrder($vendiroOrder);

        if ($newOrderId) {
            $this->orderStatusManager->createInvoice($newOrderId);
            $this->updateOrderCommentAndStatus($newOrderId, $vendiroOrder);

            return $this->orderStatusManager->getIncrementId($newOrderId);
        }
    }

    /**
     * @param $vendiroOrder
     *
     * @return bool|int
     * @throws \TIG\Vendiro\Exception
     */
    private function prepareAndPlaceOrder($vendiroOrder)
    {
        if ($this->coreSession->getFulfilmentByMarketplace() == true) {
            // @codingStandardsIgnoreLine
            $exceptionMessage = __('Fulfilment by marketplace flag already set, this means another order is busy.');
            throw new VendiroException($exceptionMessage);
        }

        if (isset($vendiroOrder['fulfilment_by_marketplace']) && $vendiroOrder['fulfilment_by_marketplace'] == 'true') {
            $this->coreSession->setFulfilmentByMarketplace(true);
        }

        $newOrderId = $this->placeOrder($vendiroOrder['id']);

        $this->coreSession->unsFulfilmentByMarketplace();

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
        $quoteProductData = $this->product->createProductDataFromApiData($apiProduct);
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
     * @throws VendiroException
     */
    private function placeOrder($vendiroId)
    {
        $newOrderId = false;

        try {
            $newOrderId = $this->cart->placeOrder($vendiroId);
        } catch (\Exception $exception) {
            throw new VendiroException(__($exception->getMessage()));
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
            $comment .= "<br/>Fulfilment by Marketplace";
        }

        $this->orderStatusManager->addHistoryComment($magentoOrderId, $comment);
    }
}
