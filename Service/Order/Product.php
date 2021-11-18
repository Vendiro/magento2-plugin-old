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
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use TIG\Vendiro\Exception as VendiroException;
use TIG\Vendiro\Logging\Log;

class Product
{
    /** @var DataObjectFactory */
    private $dataObjectFactory;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var StockState */
    private $stockState;

    /** @var Log */
    private $logger;

    public function __construct(
        DataObjectFactory $dataObjectFactory,
        ProductRepositoryInterface $productRepository,
        StockState $stockState,
        Log $logger
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->productRepository = $productRepository;
        $this->stockState = $stockState;
        $this->logger = $logger;
    }

    public function createProductDataFromApiData($apiProduct)
    {
        $data = [
            'qty' => (int)$apiProduct['amount'],
            'custom_price' => $apiProduct['value'],
        ];

        $quoteProductData = $this->dataObjectFactory->create($data);

        return $quoteProductData;
    }

    /**
     * @param $sku
     * @param $storeId
     *
     * @return ProductInterface
     * @throws VendiroException
     */
    public function getBySku($sku, $storeId = null)
    {
        $product = $this->loadProduct($sku, $storeId);

        if (!$product->getId()) {
            $errorMessage = __(
                "The order could not be imported. The requested product wasn't found. [SKU: %1]", $sku
            );
            throw new VendiroException($errorMessage);
        }

        return $product;
    }

    /**
     * @param $sku
     * @param int|null $storeId
     *
     * @return ProductInterface
     * @throws VendiroException
     */
    private function loadProduct($sku, $storeId = null)
    {
        try {
            $product = $this->productRepository->get($sku, false, $storeId);
        } catch (NoSuchEntityException $exception) {
            $this->logger->critical('Vendiro load product went wrong: ' . $exception->getMessage(), ['sku' => $sku]);

            $errorMessage = __(
                "The order could not be imported. The requested product wasn't found. [SKU: %1]", $sku
            );
            throw new VendiroException($errorMessage);
        }

        return $product;
    }
}
