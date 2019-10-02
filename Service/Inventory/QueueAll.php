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
namespace TIG\Vendiro\Service\Inventory;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use TIG\Vendiro\Api\StockRepositoryInterface;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\Config\Provider\QueueStatus;

class QueueAll
{
    /** @var ProductCollectionFactory */
    private $productCollectionFactory;

    /** @var StockRepositoryInterface */
    private $stockRepository;

    /** @var Log */
    private $logger;

    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        StockRepositoryInterface $stockRepository,
        Log $logger
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->stockRepository = $stockRepository;
        $this->logger = $logger;
    }

    /**
     * @return bool
     */
    public function queueAll()
    {
        $this->updateQueuedProducts();

        $productCollection = $this->getProductsToQueue();

        $existingSkus = $this->stockRepository->fetchExistingSkus();
        $newData = [];

        foreach ($productCollection as $product) {
            $productData = $this->getProductData($product, $existingSkus);
            $newData = array_merge($newData, $productData);
        }

        $result = $this->insertProductData($newData);

        return $result;
    }

    public function updateQueuedProducts()
    {
        $data = ['status' => QueueStatus::QUEUE_STATUS_FORCE_STOCK_UPDATE];
        $condition = ['status != ?' => QueueStatus::QUEUE_STATUS_NEW];

        try {
            $this->stockRepository->updateMultiple($data, $condition);
        } catch (LocalizedException $exception) {
            $this->logger->notice('Could not update queued products: ' . $exception->getMessage());
        }
    }

    /**
     * @return Collection
     */
    public function getProductsToQueue()
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect('sku');
        $productCollection->addAttributeToSelect('type_id');
        $productCollection->load();

        return $productCollection;
    }

    /**
     * @param Product|ProductInterface $product
     * @param array                    $existingSkus
     *
     * @return array
     */
    private function getProductData($product, $existingSkus)
    {
        $data = [];

        if ($product->getTypeId() == 'configurable' || $product->getTypeId() == 'bundle'
            || in_array($product->getSku(), $existingSkus)
        ) {
            return $data;
        }

        $data = ['product_sku' => $product->getSku(), 'status' => QueueStatus::QUEUE_STATUS_FORCE_STOCK_UPDATE];

        return [$data];
    }

    /**
     * @param array $productData
     *
     * @return bool
     */
    private function insertProductData($productData)
    {
        if (empty($productData)) {
            return true;
        }

        try {
            $this->stockRepository->insertMultiple($productData);
        } catch (LocalizedException $exception) {
            $this->logger->notice('Could not queue products: ' . $exception->getMessage());

            return false;
        }

        return true;
    }
}
