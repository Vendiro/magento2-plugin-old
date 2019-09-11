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

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use TIG\Vendiro\Api\Data\StockInterface;
use TIG\Vendiro\Api\StockRepositoryInterface;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\Config\Provider\QueueStatus;

class StockQueue
{
    /** @var StockRepositoryInterface */
    private $stockRepository;

    /** @var Log */
    private $logger;

    /**
     * @param StockRepositoryInterface $stockRepository
     * @param Log                      $logger
     */
    public function __construct(
        StockRepositoryInterface $stockRepository,
        Log $logger
    ) {
        $this->stockRepository = $stockRepository;
        $this->logger = $logger;
    }

    /**
     * @param string $sku
     */
    public function saveOrUpdateQueueBySku($sku)
    {
        try {
            $vendiroStock = $this->getStockObject($sku);
            $this->stockRepository->save($vendiroStock);
        } catch (NoSuchEntityException $exception) {
            $errorMessage = 'Product SKU ' . $sku . ' could not be queued: ' . $exception->getMessage();
            $this->logger->critical($errorMessage);
        } catch (CouldNotSaveException $exception) {
            $errorMessage = 'Product SKU ' . $sku . ' could not be queued' . $exception->getMessage();
            $this->logger->critical($errorMessage);
        }
    }

    /**
     * @param string $sku
     *
     * @return StockInterface
     * @throws NoSuchEntityException
     */
    private function getStockObject($sku)
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
