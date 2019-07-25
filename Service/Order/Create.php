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
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Service\Order\Create\CartManager;

class Create
{
    /** @var CartManager */
    private $cart;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var DataObjectFactory */
    private $dataObjectFactory;

    /** @var Log */
    private $logger;

    /**
     * @param CartManager                $cart
     * @param ProductRepositoryInterface $productRepository
     * @param Log                        $logger
     */
    public function __construct(
        CartManager $cart,
        ProductRepositoryInterface $productRepository,
        DataObjectFactory $dataObjectFactory,
        Log $logger
    ) {
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->dataObjectFactory = $dataObjectFactory;
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

        foreach ($vendiroOrder['orderlines'] as $apiProduct) {
            $this->addProducts($apiProduct);
        }

        $this->cart->addAddress($vendiroOrder['invoice_address'], 'Billing');
        $this->cart->addAddress($vendiroOrder['delivery_address'], 'Shipping');
        $this->cart->setShippingMethod('flatrate_flatrate');
        $this->cart->setPaymentMethod('checkmo');

        try {
            $newOrderId = $this->cart->placeOrder();
        } catch (\Exception $exception) {
            $this->logger->critical('Vendiro import went wrong: ' . $exception->getMessage());
        }

        return $newOrderId;
    }

    /**
     * @param $apiProduct
     */
    private function addProducts($apiProduct)
    {
        $data = [
            'qty' => (int)$apiProduct['amount'],
            'custom_price' => $apiProduct['value'],
        ];

        try {
            $product = $this->productRepository->get($apiProduct['sku']);
            $quoteProductData = $this->dataObjectFactory->create($data);

            $this->cart->addProduct($product, $quoteProductData);
        } catch (NoSuchEntityException $exception) {
            $this->logger->critical('Vendiro import went wrong: ' . $exception->getMessage());
        }
    }
}
