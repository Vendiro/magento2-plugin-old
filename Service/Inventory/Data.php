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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\Vendiro\Service\Inventory;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use TIG\Vendiro\Api\Data\StockInterface;
use TIG\Vendiro\Api\StockRepositoryInterface;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Model\Config\Provider\QueueStatus;
use TIG\Vendiro\Webservices\Endpoints\UpdateProductsStock;

class Data
{
    /** @var ApiConfiguration */
    private $apiConfiguration;

    /** @var StockRepositoryInterface */
    private $stockRepository;

    /** @var ProductStock */
    private $productStock;

    /** @var UpdateProductsStock */
    private $updateProductsStock;

    /** @var Log */
    private $logger;

    /**
     * @param ApiConfiguration         $apiConfiguration
     * @param StockRepositoryInterface $stockRepository
     * @param ProductStock             $productStock
     * @param UpdateProductsStock      $updateProductsStock
     * @param Log                      $logger
     */
    public function __construct(
        ApiConfiguration $apiConfiguration,
        StockRepositoryInterface $stockRepository,
        ProductStock $productStock,
        UpdateProductsStock $updateProductsStock,
        Log $logger
    ) {
        $this->apiConfiguration = $apiConfiguration;
        $this->stockRepository = $stockRepository;
        $this->productStock = $productStock;
        $this->updateProductsStock = $updateProductsStock;
        $this->logger = $logger;
    }

    public function forceUpdateProductInventory()
    {
        $forcedStocks = $this->stockRepository->getForcedStock();

        if (!$this->apiConfiguration->canUpdateInventory() || empty($forcedStocks)) {
            return;
        }

        list($requestData, $updatedSkus) = $this->getRequestData($forcedStocks);

        $this->updateProductsStock->setRequestData($requestData);
        $response = $this->updateProductsStock->call();

        if (!isset($response['count_processed_skus'])) {
            $this->logApiMessage($response['message']);
            return;
        }

        if (!empty($updatedSkus)) {
            $this->stockRepository->deleteMultipleBySku($updatedSkus);
        }
    }

    public function updateProductInventory()
    {
        $newStocks = $this->stockRepository->getNewStock();

        if (!$this->apiConfiguration->canUpdateInventory() || empty($newStocks)) {
            return;
        }

        list($requestData) = $this->getRequestData($newStocks);

        $this->updateProductsStock->setRequestData($requestData);
        $response = $this->updateProductsStock->call();

        $this->processResponse($response, $newStocks);
    }

    /**
     * @param StockInterface[] $stocks
     *
     * @return array
     * @throws InputException
     * @throws LocalizedException
     */
    private function getRequestData($stocks)
    {
        $requestData = [];
        $updatedSkus = [];

        foreach ($stocks as $stock) {
            $sku = $stock->getProductSku();
            $qty = $this->productStock->getStockBySku($sku);
            $requestData[] = ['sku' => $sku, 'stock' => $qty];
            $updatedSkus[] = $sku;
        }

        return [$requestData, $updatedSkus];
    }

    /**
     * @param array                  $response
     * @param StockInterface[]|array $stockQueue
     */
    private function processResponse($response, $stockQueue)
    {
        if (!isset($response['count_processed_skus'])) {
            $this->logApiMessage($response['message']);
            return;
        }

        if ((int)$response['count_invalid_skus'] > 0 && isset($response['invalid_skus'])) {
            $this->logInvalidSkus($response['invalid_skus']);
        }

        foreach ($stockQueue as $stock) {
            $this->updateStockQueue($stock);
        }
    }

    /**
     * @param string $message
     */
    private function logApiMessage($message)
    {
        // @codingStandardsIgnoreLine
        $criticalString = sprintf(__("The Vendiro endpoint API reported an error: %s"), $message);
        $this->logger->critical($criticalString);
    }

    /**
     * @param array $invalidSkus
     */
    private function logInvalidSkus($invalidSkus)
    {
        if (!is_array($invalidSkus)) {
            $invalidSkus = [$invalidSkus];
        }

        $invalidSkusString = implode(', ', $invalidSkus);
        $noticeString = sprintf(__("The inventory of some SKU's could not be updated: %s"), $invalidSkusString);

        $this->logger->notice($noticeString);
    }

    /**
     * @param StockInterface $stock
     */
    private function updateStockQueue($stock)
    {
        $stock->setStatus(QueueStatus::QUEUE_STATUS_STOCK_UPDATED);

        try {
            $this->stockRepository->save($stock);
        } catch (CouldNotSaveException $exception) {
            $noticeString = __("Vendiro stock notice: Could not update the stock queue for SKU %s");
            $this->logger->notice(sprintf($noticeString, $stock->getProductSku()));
        }
    }
}
