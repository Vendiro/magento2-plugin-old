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
namespace TIG\Vendiro\Service\Invoice;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepository;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use TIG\Vendiro\Api\Data\OrderInterface;
use TIG\Vendiro\Api\Data\TrackQueueInterface;
use TIG\Vendiro\Api\MarketplaceRepositoryInterface;
use TIG\Vendiro\Api\TrackQueueRepositoryInterface;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\Config\Provider\QueueStatus;
use TIG\Vendiro\Model\MarketplaceRepository;
use TIG\Vendiro\Model\TrackQueueRepository;

class Validate
{
    /** @var Log $logger */
    private $logger;

    /** @var SerializerInterface */
    private $serializer;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var MagentoOrderRepository */
    private $magentoOrderRepository;

    /** @var MarketplaceRepositoryInterface|MarketplaceRepository */
    private $marketplaceRepository;

    /** @var TrackQueueRepositoryInterface|TrackQueueRepository */
    private $trackQueueItemRepository;

    /**
     * @param Log                            $logger
     * @param SerializerInterface            $serializer
     * @param SearchCriteriaBuilder          $searchCriteriaBuilder
     * @param MagentoOrderRepository         $magentoOrderRepository
     * @param MarketplaceRepositoryInterface $marketplaceRepository
     * @param TrackQueueRepositoryInterface  $trackQueueItemRepository
     */
    public function __construct(
        Log $logger,
        SerializerInterface $serializer,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        MagentoOrderRepository $magentoOrderRepository,
        MarketplaceRepositoryInterface $marketplaceRepository,
        TrackQueueRepositoryInterface $trackQueueItemRepository
    ) {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->magentoOrderRepository = $magentoOrderRepository;
        $this->marketplaceRepository = $marketplaceRepository;
        $this->trackQueueItemRepository = $trackQueueItemRepository;
    }

    /**
     * @param OrderInterface $vendirOrder
     *
     * @return bool|Invoice
     */
    public function getInvoice($vendirOrder)
    {
        if (!$this->validateMarketplace($vendirOrder->getMarketplaceId())) {
            return false;
        }

        $searchCriteria = $this->searchCriteriaBuilder->addFilter('increment_id', $vendirOrder->getOrderId(), 'eq');
        $orderResult = $this->magentoOrderRepository->getList($searchCriteria->create());

        if ($orderResult->getTotalCount() < 1) {
            return false;
        }

        /** @var Order $magentoOrder */
        $magentoOrder = $orderResult->getFirstItem();

        if (!$this->validateShipment($magentoOrder)) {
            return false;
        }

        $invoiceCollection = $magentoOrder->getInvoiceCollection();

        if ($invoiceCollection->getTotalCount() < 1) {
            return false;
        }

        /** @var Invoice $invoice */
        $invoice = $invoiceCollection->getFirstItem();

        return $invoice;
    }

    /**
     * @param $marketplaceId
     *
     * @return bool
     */
    private function validateMarketplace($marketplaceId)
    {
        $marketplace = $this->marketplaceRepository->getByMarketplaceId($marketplaceId);

        if (!$marketplace || $marketplace->getEntityId() < 1) {
            return false;
        }

        $allowedDocuments = $this->serializer->unserialize($marketplace->getAllowedDocumentTypes());
        $invoiceAllowed = false;

        foreach ($allowedDocuments as $document) {
            $invoiceAllowed = ($document == 'invoice' ? true : $invoiceAllowed);
        }

        return $invoiceAllowed;
    }

    /**
     * @param Order $magentoOrder
     *
     * @return bool
     */
    private function validateShipment($magentoOrder)
    {
        $shipmentCollection = $magentoOrder->getShipmentsCollection();

        if ($shipmentCollection->getTotalCount() < 1) {
            return false;
        }

        $valid = false;
        $shipment = $shipmentCollection->getFirstItem();
        $tracks = $shipment->getTracks();

        foreach ($tracks as $track) {
            $valid = $this->validateTracking($track);
        }

        return $valid;
    }

    /**
     * @param ShipmentTrackInterface $track
     *
     * @return bool
     */
    private function validateTracking($track)
    {
        $trackId = $track->getEntityId();
        $trackItems = $this->trackQueueItemRepository->getByFieldWithValue('track_id', $trackId, 'null');

        if (!is_array($trackItems) || empty($trackItems)) {
            return false;
        }

        $valid = false;

        /** @var TrackQueueInterface $trackItem */
        foreach ($trackItems as $trackItem) {
            $valid = ($valid ? $valid : $trackItem->getStatus() != QueueStatus::QUEUE_STATUS_NEW);
        }

        return $valid;
    }
}
