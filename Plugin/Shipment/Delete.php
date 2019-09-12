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

use TIG\Vendiro\Api\TrackQueueRepositoryInterface;
use TIG\Vendiro\Model\ResourceModel\TrackQueue;
use TIG\Vendiro\Model\TrackQueueRepository;

class Delete
{
    /** @var TrackQueueRepository $trackQueueRepository */
    private $trackQueueRepository;

    /** @var TrackQueue $trackQueueResourceModel */
    private $trackQueueResourceModel;

    public function __construct(
        TrackQueueRepositoryInterface $trackQueueRepository,
        TrackQueue $trackQueueResourceModel

    ) {
        $this->trackQueueRepository = $trackQueueRepository;
        $this->trackQueueResourceModel = $trackQueueResourceModel;
    }

    public function afterDelete($subject)
    {
        $tracks = $this->trackQueueRepository->getByFieldWithValue('track_id', $subject->getId());

        /** @var \TIG\Vendiro\Model\TrackQueue $track */
        $track = array_pop($tracks);

        $this->trackQueueResourceModel->delete($track);
    }
}
