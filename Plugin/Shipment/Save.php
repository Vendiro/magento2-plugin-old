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
namespace TIG\Vendiro\Plugin\Shipment;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentInterface;
use TIG\Vendiro\Api\TrackQueueRepositoryInterface;
use TIG\Vendiro\Model\Config\Provider\General\CarrierConfiguration;
use TIG\Vendiro\Model\Config\Provider\QueueStatus;
use TIG\Vendiro\Model\TrackQueueRepository;

class Save
{
    /** @var TrackQueueRepository $trackQueueRepository */
    private $trackQueueRepository;

    /** @var CarrierConfiguration $configuration */
    private $configuration;

    /**
     * @param CarrierConfiguration                   $configuration
     * @param TrackQueueRepositoryInterface          $trackQueueRepository
     */
    public function __construct(
        CarrierConfiguration $configuration,
        TrackQueueRepositoryInterface $trackQueueRepository
    ) {
        $this->configuration          = $configuration;
        $this->trackQueueRepository   = $trackQueueRepository;
    }

    /**
     * @param      $subject
     *
     * @param null $shipment
     *
     * @return ShipmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave($subject, $shipment = null)
    {
        $order = null;
        if ($shipment) {
            $order = $shipment->getOrder();
        }

        if (!$order) {
            $order = $subject->getOrder();
        }

        if ($order->getShippingMethod() != 'tig_vendiro_shipping') {
            return $subject;
        }

        if (!$shipment) {
            $shipment = $subject;
        }

        $this->saveVendiroCarrier($shipment);
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function saveVendiroCarrier(ShipmentInterface $shipment)
    {
        $defaultCarrier = $this->configuration->getDefaultCarrier($shipment->getStoreId());
        $shipment->setVendiroCarrier($defaultCarrier);

        if ($shipment->getVendiroCarrier() == '0') {
            $errorMessage = __(
                "Please select a default shipping method."
            );
            throw new LocalizedException($errorMessage);
        }
    }

    /**
     * @param $subject
     *
     * @throws \Exception
     */
    public function afterSave($subject, $shipment = null)
    {
        $tracks = $this->getTracks($subject, $shipment);
        $this->saveTracks($tracks);
    }

    /**
     * @param      $subject
     * @param null $shipment
     *
     * @return null
     * @throws \Exception
     */
    public function getTracks($subject, $shipment = null)
    {
        $tracks = null;
        if ($shipment) {
            $tracks = $shipment->getTracks();
        }

        if (!$tracks) {
            $tracks = $subject->getTracks();
        }

        $tracks = $this->filterTracks($tracks);
        $tracks = array_unique($tracks, SORT_REGULAR);

        return $tracks;
    }

    /**
     * @param $tracks
     *
     * @return mixed
     * @throws \Exception
     */
    public function filterTracks($tracks)
    {
        $entityIds = [];

        foreach ($tracks as $track) {
            array_push($entityIds, $track->getId());
        }

        $duplicateTracks = $this->trackQueueRepository->getByFieldWithValue('track_id', $entityIds, 0, 'in');

        if (!isset($duplicateTracks)) {
            return $tracks;
        }

        foreach ($duplicateTracks as $duplicateTrack) {
            unset($tracks[$duplicateTrack->getTrackId()]);
        }

        return $tracks;
    }

    /**
     * @param $tracks
     *
     * @throws \Exception
     */
    public function saveTracks($tracks)
    {
        foreach ($tracks as $track) {
            $vendiroTrackQueueItem = $this->trackQueueRepository->create();
            $vendiroTrackQueueItem->setTrackId($track->getId());
            $vendiroTrackQueueItem->setStatus(QueueStatus::QUEUE_STATUS_NEW);
            // @codingStandardsIgnoreLine
            $vendiroTrackQueueItem->save();
        }
    }
}
