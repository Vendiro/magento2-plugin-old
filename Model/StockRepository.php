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
namespace TIG\Vendiro\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use TIG\Vendiro\Api\Data\StockInterface;
use TIG\Vendiro\Api\StockRepositoryInterface;
use TIG\Vendiro\Model\Config\Provider\QueueStatus;
use TIG\Vendiro\Model\ResourceModel\Stock\CollectionFactory;

class StockRepository extends AbstractRepository implements StockRepositoryInterface
{
    /**
     * @var StockFactory $stockFactory
     */
    private $stockFactory;

    public function __construct(
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StockFactory $stockFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->stockFactory = $stockFactory;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($searchResultsFactory, $searchCriteriaBuilder);
    }

    /**
     * {@inheritDoc}
     */
    public function save(StockInterface $stock)
    {
        try {
            $stock->setUpdatedAt(null); //updated_at should be updated by mysql itself
            $stock->save();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $stock;
    }

    /**
     * {@inheritDoc}
     */
    public function getById($entityId)
    {
        $stock = $this->create();
        $stock->load($entityId);

        if (!$stock->getId()) {
            // @codingStandardsIgnoreLine
            throw new NoSuchEntityException(__('Stock with id "%1" does not exist.', $entityId));
        }

        return $stock;
    }

    /**
     * {@inheritDoc}
     */
    public function getBySku($sku)
    {
        $foundStock = $this->getByFieldWithValue('product_sku', $sku);

        if (is_array($foundStock)) {
            $foundStock = array_shift($foundStock);
        }

        return $foundStock;
    }

    /**
     * {@inheritDoc}
     */
    public function getNewStock()
    {
        return $this->getByFieldWithValue('status', QueueStatus::QUEUE_STATUS_NEW, null);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(StockInterface $stock)
    {
        try {
            $stock->delete();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteById($entityId)
    {
        $stock = $this->getById($entityId);

        return $this->delete($stock);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data = [])
    {
        return $this->stockFactory->create($data);
    }
}
