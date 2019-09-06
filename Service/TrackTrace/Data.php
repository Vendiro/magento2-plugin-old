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
use TIG\Vendiro\Model\OrderRepository;
use TIG\Vendiro\Model\TrackQueueRepository;
use TIG\Vendiro\Webservices\Endpoints\ConfirmShipment;
use TIG\Vendiro\Logging\Log;

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
     *
     * @return mixed
     * @throws \TIG\Vendiro\Exception
     */
    public function confirmShipmentCall($vendiroOrderId, $carrierId, $shipmentCode)
    {
        $requestData = ['carrier_id' => '99999', 'shipment_code' => $shipmentCode];
        $this->confirmShipment->setRequestData($requestData);

        $result = $this->confirmShipment->call($vendiroOrderId);

        if ($result) {
            throw new \TIG\Vendiro\Exception(__($result['message']));
        }

        return $result;
    }

    /**
     * @param \TIG\Vendiro\Api\Data\TrackQueueInterface $trackQueueItem
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTracks($trackQueueItem)
    {
        $data = [];

        $trackCollection = $this->collectionFactory->create();
        $trackId = $trackQueueItem->getTrackId();
        $track = $trackCollection->getItemById($trackId);

        $shipmentCode = $track->getTrackNumber();

        $orderId = $track->getShipment()->getOrderId();
        $vendiroOrder = $this->orderRepository->getByOrderId($orderId);
        $entityId = array_keys($vendiroOrder)['0'];
        $vendiroOrderId = $this->orderRepository->getById($entityId)->getVendiroId();
        $carrierId = $this->carrierConfiguration->getDefaultCarrier($this->shipmentInterface->getStoreId());

        $data['shipment_code'] = $shipmentCode;
        $data['vendiro_order_id'] = $vendiroOrderId;
        $data['carrier_id'] = $carrierId;

        return $data;
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
            $this->confirmShipmentCall($data['vendiro_order_id'], $data['carrier_id'], $data['shipment_code']);
            $this->saveTrackItem($trackQueueItem);
        } catch (\Magento\Framework\Exception\CouldNotSaveException $exception) {
            $this->logger->addNotice('Could not confirm Vendiro shipment');
        } catch (\TIG\Vendiro\Exception $exception) {
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
