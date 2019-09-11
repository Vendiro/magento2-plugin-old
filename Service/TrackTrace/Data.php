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

namespace TIG\Vendiro\Service\TrackTrace;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory;
use TIG\Vendiro\Exception as VendiroException;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\Config\Provider\General\CarrierConfiguration;
use TIG\Vendiro\Model\Config\Provider\QueueStatus;
use TIG\Vendiro\Model\OrderRepository;
use TIG\Vendiro\Model\TrackQueueRepository;
use TIG\Vendiro\Webservices\Endpoints\ConfirmShipment;

//@codingStandardsIgnoreFile
class Data
{
    /** @var ConfirmShipment $confirmShipment */
    private $confirmShipment;

    /** @var TrackQueueRepository $trackQueueItemRepository */
    private $trackQueueItemRepository;

    /** @var CollectionFactory $collectionFactory */
    private $collectionFactory;

    /** @var CarrierConfiguration $carrierConfiguration */
    private $carrierConfiguration;

    /** @var ShipmentInterface $shipmentInterface */
    private $shipmentInterface;

    /** @var OrderRepository $orderRepository */
    private $orderRepository;

    /** @var Log $logger */
    private $logger;

    /**
     * @param ConfirmShipment          $confirmShipment
     * @param TrackQueueRepository     $trackQueueItemRepository
     * @param CollectionFactory        $collectionFactory
     * @param CarrierConfiguration     $carrierConfiguration
     * @param ShipmentInterface        $shipmentInterface
     * @param OrderRepository          $orderRepository
     * @param Log                      $logger
     */
    public function __construct(
        ConfirmShipment $confirmShipment,
        TrackQueueRepository $trackQueueItemRepository,
        CollectionFactory $collectionFactory,
        CarrierConfiguration $carrierConfiguration,
        ShipmentInterface $shipmentInterface,
        OrderRepository $orderRepository,
        Log $logger
    ) {
        $this->confirmShipment = $confirmShipment;
        $this->trackQueueItemRepository = $trackQueueItemRepository;
        $this->collectionFactory = $collectionFactory;
        $this->carrierConfiguration = $carrierConfiguration;
        $this->shipmentInterface = $shipmentInterface;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * @param $vendiroOrderId
     * @param $carrierId
     * @param $shipmentCode
     * @param $carrierName
     *
     * @return mixed
     * @throws \TIG\Vendiro\Exception
     */
    public function confirmShipmentCall($vendiroOrderId, $carrierId, $shipmentCode, $carrierName)
    {
        $requestData = ['carrier_id' => $carrierId, 'shipment_code' => $shipmentCode, 'carrier_name' => $carrierName];
        $this->confirmShipment->setRequestData($requestData);

        $result = $this->confirmShipment->call($vendiroOrderId);

        if ($result['message']) {
            throw new VendiroException(__($result['message']));
        }

        return $result;
    }

    /**
     * @param $trackQueueItem
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTracks($trackQueueItem)
    {
        $data = [];

        $track = $this->getTrack($trackQueueItem);

        $shipmentCode = $track->getTrackNumber();
        $vendiroOrderId = $this->getVendiroOrderId($trackQueueItem);
        $carrierId = $this->getCarrier();
        $carrierName = $this->getCarrierName($trackQueueItem);

        $data['shipment_code'] = $shipmentCode;
        $data['vendiro_order_id'] = $vendiroOrderId;
        $data['carrier_id'] = $carrierId;
        $data['carrier_name'] = $carrierName;

        return $data;
    }

    /**
     * @param $trackQueueItem
     *
     * @return
     */
    public function getCarrierName($trackQueueItem)
    {
        $track = $this->getTrack($trackQueueItem);

        return $track->getTitle();
    }

    /**
     * @param $trackQueueItem
     *
     * @return \Magento\Framework\DataObject
     */
    public function getTrack($trackQueueItem)
    {
        $trackCollection = $this->collectionFactory->create();
        $trackId = $trackQueueItem->getTrackId();
        $track = $trackCollection->getItemById($trackId);

        return $track;
    }

    /**
     * @param $trackQueueItem
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getVendiroOrderId($trackQueueItem)
    {
        $track = $this->getTrack($trackQueueItem);
        $orderId = $track->getShipment()->getOrderId();
        $vendiroOrder = $this->orderRepository->getByOrderId($orderId);
        $entityId = array_keys($vendiroOrder)['0'];
        $vendiroOrderId = $this->orderRepository->getById($entityId)->getVendiroId();

        return $vendiroOrderId;
    }

    public function getCarrier()
    {
        $storeId = $this->shipmentInterface->getStoreId();
        $carrierId = $this->carrierConfiguration->getDefaultCarrier($storeId);

        return $carrierId;
    }

    /**
     * @param $trackQueueItem
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function shipmentCall($trackQueueItem)
    {
        $data = $this->getTracks($trackQueueItem);

        try {
            $this->confirmShipmentCall($data['vendiro_order_id'], $data['carrier_id'], $data['shipment_code'], $data['carrier_name']);
            $this->saveTrackItem($trackQueueItem);
        } catch (CouldNotSaveException $exception) {
            $this->logger->addNotice('Could not confirm Vendiro shipment');
        } catch (VendiroException $exception) {
            $this->logger->notice($exception->getMessage());
        }
    }

    /**
     * @param \TIG\Vendiro\Api\Data\TrackQueueInterface $trackQueueItem
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveTrackItem($trackQueueItem)
    {
        $trackQueueItem->setStatus(QueueStatus::QUEUE_STATUS_SHIPMENT_CREATED);
        $this->trackQueueItemRepository->save($trackQueueItem);
    }
}
