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
namespace TIG\Vendiro\Plugin\StockQueue\Catalog;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use TIG\Vendiro\Api\Data\StockInterface;
use TIG\Vendiro\Api\StockRepositoryInterface;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Model\Config\Provider\QueueStatus;

class StockItemRepository
{
    /** @var ApiConfiguration */
    private $apiConfiguration;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var StockRepositoryInterface */
    private $stockRepository;

    /** @var Log */
    private $logger;

    public function __construct(
        ApiConfiguration $apiConfiguration,
        ProductRepositoryInterface $productRepository,
        StockRepositoryInterface $stockRepository,
        Log $logger
    ) {
        $this->apiConfiguration = $apiConfiguration;
        $this->productRepository = $productRepository;
        $this->stockRepository = $stockRepository;
        $this->logger = $logger;
    }

    /**
     * @param StockItemRepositoryInterface $subject
     * @param                              $result
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    public function afterSave(StockItemRepositoryInterface $subject, $result)
    {
        if (!$this->apiConfiguration->canUpdateInventory()) {
            return $result;
        }

        try {
            $productId = $result->getProductId();
            $magentoProduct = $this->productRepository->getById($productId);

            $vendiroStock = $this->createVendiroStock($magentoProduct->getSku());
            $this->stockRepository->save($vendiroStock);
        } catch (NoSuchEntityException $exception) {
            $errorMessage = 'Stock queue on Product #' . $productId . ' went wrong: ' . $exception->getMessage();
            $this->logger->critical($errorMessage);
        } catch (CouldNotSaveException $exception) {
            $errorMessage = 'Stock queue on Product #' . $productId . ' went wrong: ' . $exception->getMessage();
            $this->logger->critical($errorMessage);
        }

        return $result;
    }

    /**
     * @param string $sku
     *
     * @return StockInterface
     * @throws NoSuchEntityException
     */
    private function createVendiroStock($sku)
    {
        $existingStock = $this->stockRepository->getBySku($sku);

        if ($existingStock && $existingStock->getEntityId() > 0) {
            $existingStock->setStatus(QueueStatus::QUEUE_STATUS_NEW);

            return $existingStock;
        }

        $vendiroStock = $this->stockRepository->create();
        $vendiroStock->setProductSku($sku);
        $vendiroStock->setStatus(QueueStatus::QUEUE_STATUS_NEW);

        return $vendiroStock;
    }
}
