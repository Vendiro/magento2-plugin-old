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

use Magento\Sales\Api\Data\ShipmentInterface;
use TIG\Vendiro\Api\TrackQueueRepositoryInterface;
use TIG\Vendiro\Model\Config\Provider\General\Configuration;
use TIG\Vendiro\Model\Config\Provider\QueueStatus;
use TIG\Vendiro\Model\TrackQueueRepository;

class Save
{
    /** @var TrackQueueRepository $trackQueueRepository */
    private $trackQueueRepository;

    /** @var Configuration $configuration */
    private $configuration;

    /**
     * @param Configuration                          $configuration
     * @param TrackQueueRepositoryInterface          $trackQueueRepository
     */
    public function __construct(
        Configuration $configuration,
        TrackQueueRepositoryInterface $trackQueueRepository
    ) {
        $this->configuration          = $configuration;
        $this->trackQueueRepository   = $trackQueueRepository;
    }

    /**
     * @param      $subject
     * @param null $shipment
     *
     * @return mixed
     */
    public function beforeSave($subject, $shipment = null)
    {
        if (!$shipment) {
            $shipment = $subject;
        }

        $order = $shipment->getOrder();

        if ($order->getShippingMethod() === 'tig_vendiro_shipping') {
            $this->saveVendiroCarrier($shipment);
        }

        return null;
    }

    /**
     * @param ShipmentInterface $shipment
     */
    private function saveVendiroCarrier(ShipmentInterface $shipment)
    {
        $defaultCarrier = $this->configuration->getDefaultCarrier($shipment->getStoreId());
        $shipment->setVendiroCarrier($defaultCarrier);
    }

    /**
     * @param      $subject
     * @param null $shipment
     *
     * @return null
     * @throws \Exception
     */
    public function afterSave($subject, $shipment = null)
    {
        $order = $shipment->getOrder();
        $shippingMethod = $order->getShippingMethod();

        if ($shippingMethod === 'tig_vendiro_shipping') {
            $tracks = $this->getTracks($subject, $shipment);

            foreach ($tracks as $track) {
                $this->saveTrack($track);
            }
        }

        return $shipment;
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
     * @param $track
     *
     * @throws \Exception
     */
    public function saveTrack($track)
    {
        $vendiroTrackQueueItem = $this->trackQueueRepository->create();
        $vendiroTrackQueueItem->setTrackId($track->getId());
        $vendiroTrackQueueItem->setStatus(QueueStatus::QUEUE_STATUS_NEW);

        $this->trackQueueRepository->save($vendiroTrackQueueItem);
    }
}
