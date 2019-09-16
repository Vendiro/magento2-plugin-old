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

namespace TIG\Vendiro\Cron;

use TIG\Vendiro\Api\TrackQueueRepositoryInterface;
use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Model\TrackQueueRepository;
use TIG\Vendiro\Service\TrackTrace\Data;

class TrackTrace
{
    /** @var TrackQueueRepository $trackQueueItemRepository */
    private $trackQueueItemRepository;

    /** @var ApiConfiguration $apiConfiguration */
    private $apiConfiguration;

    /** @var Data $shipmentService */
    private $shipmentService;

    /**
     * @param Data                                         $shipmentService
     * @param ApiConfiguration                             $apiConfiguration
     * @param TrackQueueRepositoryInterface                $trackQueueItemRepository
     */
    public function __construct(
        Data $shipmentService,
        ApiConfiguration $apiConfiguration,
        TrackQueueRepositoryInterface $trackQueueItemRepository
    ) {
        $this->shipmentService = $shipmentService;
        $this->apiConfiguration = $apiConfiguration;
        $this->trackQueueItemRepository = $trackQueueItemRepository;
    }

    /**
     * @param \TIG\Vendiro\Api\Data\TrackQueueInterface $trackQueueItem
     *
     */
    public function confirmShipment($trackQueueItem)
    {
        if (!$this->apiConfiguration->canRegisterShipments()) {
            return;
        }

        $queueItems = $this->trackQueueItemRepository->getQueueItems();

        if (!is_array($queueItems)) {
            return;
        }

        foreach ($queueItems as $trackQueueItem) {
            $this->shipmentService->shipmentCall($trackQueueItem);
        }
    }
}
