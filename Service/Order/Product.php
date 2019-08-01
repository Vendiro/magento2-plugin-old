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

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Model\StockState;
use Magento\Framework\Exception\NoSuchEntityException;
use TIG\Vendiro\Exception as VendiroException;
use TIG\Vendiro\Logging\Log;

class Product
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var StockState */
    private $stockState;

    /** @var Log */
    private $logger;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        StockState $stockState,
        Log $logger
    ) {
        $this->productRepository = $productRepository;
        $this->stockState = $stockState;
        $this->logger = $logger;
    }

    /**
     * @param $sku
     * @param $store
     *
     * @return ProductInterface
     * @throws VendiroException
     */
    public function getBySku($sku, $store = null)
    {
        $product = $this->loadProduct($sku);

        if (!$product->getId()) {
            $errorMessage = __("The order could not be exported. The product that was requested wasn't found.");
            throw new VendiroException($errorMessage);
        }

        $qty = $this->stockState->getStockQty($product->getId(), $store);

        if ($qty <= 0) {
            $errorMessage = __("The order could not be exported. The product that was requested is not in stock.");
            throw new VendiroException($errorMessage);
        }

        return $product;
    }

    /**
     * @param $sku
     *
     * @return ProductInterface
     * @throws VendiroException
     */
    private function loadProduct($sku)
    {
        try {
            $product = $this->productRepository->get($sku);
        } catch (NoSuchEntityException $exception) {
            $this->logger->critical('Vendiro load product went wrong: ' . $exception->getMessage());

            $errorMessage = __("The order could not be exported. The product that was requested wasn't found.");
            throw new VendiroException($errorMessage);
        }

        return $product;
    }
}
