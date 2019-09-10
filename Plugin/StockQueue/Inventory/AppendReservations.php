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
namespace TIG\Vendiro\Plugin\StockQueue\Inventory;

use Magento\InventoryReservations\Model\Reservation;
use Magento\InventoryReservationsApi\Model\AppendReservationsInterface;
use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Service\Inventory\StockQueue;

class AppendReservations
{
    /** @var ApiConfiguration */
    private $apiConfiguration;

    /** @var StockQueue */
    private $stockQueue;

    /**
     * @param ApiConfiguration $apiConfiguration
     * @param StockQueue       $stockQueue
     */
    public function __construct(
        ApiConfiguration $apiConfiguration,
        StockQueue $stockQueue
    ) {
        $this->apiConfiguration = $apiConfiguration;
        $this->stockQueue = $stockQueue;
    }

    /**
     * @param AppendReservationsInterface $subject
     * @param                             $result
     * @param array|Reservation[]         $reservations
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    public function afterExecute(AppendReservationsInterface $subject, $result, array $reservations)
    {
        if (!$this->apiConfiguration->canUpdateInventory()) {
            return $result;
        }

        foreach ($reservations as $reservation) {
            $this->stockQueue->saveOrUpdateQueueBySku($reservation->getSku());
        }

        return $result;
    }
}
