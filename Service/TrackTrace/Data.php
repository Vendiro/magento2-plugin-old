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

use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory;
use TIG\Vendiro\Exception;
use TIG\Vendiro\Model\Config\Provider\General\CarrierConfiguration;
use TIG\Vendiro\Model\Config\Provider\QueueStatus;
use TIG\Vendiro\Model\TrackQueueRepository;
use TIG\Vendiro\Webservices\Endpoints\ConfirmShipment;

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

    /**
     * @param ConfirmShipment                           $confirmShipment
     * @param TrackQueueRepository                      $trackQueueItemRepository
     * @param CollectionFactory                         $collectionFactory
     * @param CarrierConfiguration                      $carrierConfiguration
     * @param ShipmentInterface                         $shipmentInterface
     */
    public function __construct(
        ConfirmShipment $confirmShipment,
        TrackQueueRepository $trackQueueItemRepository,
        CollectionFactory $collectionFactory,
        CarrierConfiguration $carrierConfiguration,
        ShipmentInterface $shipmentInterface
    ) {
        $this->confirmShipment = $confirmShipment;
        $this->trackQueueItemRepository = $trackQueueItemRepository;
        $this->collectionFactory = $collectionFactory;
        $this->carrierConfiguration = $carrierConfiguration;
        $this->shipmentInterface = $shipmentInterface;
    }

    /**
     * @param $vendiroOrderId
     * @param $carrierId
     * @param $shipmentCode
     *
     * @return mixed
     */
    public function confirmShipmentCall($vendiroOrderId, $carrierId, $shipmentCode)
    {
        $requestData = ['carrier_id' => $carrierId, 'shipment_code' => $shipmentCode];
        $this->confirmShipment->setRequestData($requestData);

        return $this->confirmShipment->call($vendiroOrderId);
    }

    /**
     * @param \TIG\Vendiro\Api\Data\TrackQueueInterface $trackQueueItem
     *
     * @return array
     */
    public function getTracks($trackQueueItem)
    {
        $data = [];

        $trackCollection = $this->collectionFactory->create();
        $trackId = $trackQueueItem->getTrackId();
        $track = $trackCollection->getItemById($trackId);

        $shipmentCode = $track->getTrackNumber();
        $shipment = $track->getShipment();

        $vendiroOrderId = $shipment->getOrderId();
        $carrierId = $this->carrierConfiguration->getDefaultCarrier($this->shipmentInterface->getStoreId());

        $data['shipment_code'] = $shipmentCode;
        $data['vendiro_order_id'] = $vendiroOrderId;
        $data['carrier_id'] = $carrierId;

        return $data;
    }

    /**
     * @param $trackQueueItem
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \TIG\Vendiro\Exception
     */
    public function shipmentCall($trackQueueItem)
    {
        $data = $this->getTracks($trackQueueItem);

        try {
            $this->confirmShipmentCall($data['vendiro_order_id'], $data['carrier_id'], $data['shipment_code']);
        } catch (Exception $exception) {
            throw $exception;
        }

        $this->saveTrackItem($trackQueueItem);
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
