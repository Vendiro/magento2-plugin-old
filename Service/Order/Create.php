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

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\Payment\Vendiro;
use TIG\Vendiro\Service\Order\Create\Cart;

class Create
{
    /** @var Cart */
    private $cart;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var Log */
    private $logger;

    /**
     * @param Cart                       $cart
     * @param ProductRepositoryInterface $productRepository
     * @param Log                        $logger
     */
    public function __construct(
        Cart $cart,
        ProductRepositoryInterface $productRepository,
        Log $logger
    ) {
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    /**
     * @param $vendiroOrder
     *
     * @return int
     */
    public function execute($vendiroOrder)
    {
        $storeCode = $vendiroOrder['marketplace']['reference'];
        $this->cart->createCart($storeCode);

        array_walk($vendiroOrder['orderlines'], [$this, 'addProducts']);

        $this->cart->addAddress($vendiroOrder['invoice_address'], 'Billing');
        $this->cart->addAddress($vendiroOrder['delivery_address'], 'Shipping');

        $this->cart->setShippingMethod('flatrate_flatrate');
        $this->cart->setPaymentMethod(Vendiro::PAYMENT_CODE);

        $newOrderId = $this->cart->placeOrder();

        return $newOrderId;
    }

    /**
     * @param $apiProduct
     * @param $index
     */
    // @codingStandardsIgnoreLine
    private function addProducts($apiProduct, $index)
    {
        try {
            $product = $this->productRepository->get($apiProduct['sku']);
            $this->cart->addProduct($product, (int)$apiProduct['amount']);
        } catch (NoSuchEntityException $exception) {
            $this->logger->critical('Vendiro import went wrong: ' . $exception->getMessage());
        }
    }
}
