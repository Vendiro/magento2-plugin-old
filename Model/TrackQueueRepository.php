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
namespace TIG\Vendiro\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use TIG\Vendiro\Api\Data\OrderInterface;
use TIG\Vendiro\Api\Data\TrackQueueInterface;
use TIG\Vendiro\Api\TrackQueueRepositoryInterface;
use TIG\Vendiro\Model\ResourceModel\TrackQueue\CollectionFactory;

class TrackQueueRepository extends AbstractRepository implements TrackQueueRepositoryInterface
{
    const VENDIRO_SHIPMENTS_LIMIT = 'tig_vendiro/shipments_limit';

    /** @var ScopeConfigInterface $scopeInterface */
    private $scopeInterface;

    /** @var TrackQueueFactory $trackQueueFactory */
    private $trackQueueFactory;

    /**
     * @param SearchResultsInterfaceFactory                        $searchResultsFactory
     * @param SearchCriteriaBuilder                                $searchCriteriaBuilder
     * @param TrackQueueFactory                                    $trackQueueFactory
     * @param ScopeConfigInterface                                 $scopeInterface
     * @param CollectionFactory                                    $collectionFactory
     */
    public function __construct(
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TrackQueueFactory $trackQueueFactory,
        ScopeConfigInterface $scopeInterface,
        CollectionFactory $collectionFactory
    ) {
        $this->trackQueueFactory = $trackQueueFactory;
        $this->scopeInterface = $scopeInterface;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($searchResultsFactory, $searchCriteriaBuilder);
    }

    /**
     * @param TrackQueueInterface $trackQueueItem
     *
     * @return TrackQueueInterface
     * @throws CouldNotSaveException
     */
    public function save(TrackQueueInterface $trackQueueItem)
    {
        try {
            $trackQueueItem->save();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $trackQueueItem;
    }

    /**
     * @param array $data
     *
     * @return TrackQueue
     */
    public function create(array $data = [])
    {
        return $this->trackQueueFactory->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function getQueueItems()
    {
        $shipmentsLimit = $this->scopeInterface->getValue(self::VENDIRO_SHIPMENTS_LIMIT, ScopeInterface::SCOPE_STORE);

        return $this->getByFieldWithValue('status', 'new', $shipmentsLimit);
    }

    /**
     * @param $trackId
     *
     * @return TrackQueueInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByTrackId($trackId)
    {
        $track = $this->trackQueueFactory->create();
        $track->load($trackId);

        if (!$track->getId()) {
            // @codingStandardsIgnoreLine
            throw new NoSuchEntityException(__('Order with id "%1" does not exist.', $entityId));
        }

        return $track;
    }

    /**
     * @param TrackQueueInterface $trackQueue
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(TrackQueueInterface $trackQueue)
    {
        try {
            $trackQueue->delete();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }
}
