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

use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Service\Inventory\Data;
use TIG\Vendiro\Service\Inventory\QueueAll;
use TIG\Vendiro\Logging\Log;

class Stock
{
    /** @var Data $orderService */
    private $inventoryService;

    /** @var ApiConfiguration */
    private $apiConfiguration;

    /** @var QueueAll */
    private $queueAll;

    /** @var Log */
    private $logger;

    /**
     * @param Data             $inventoryService
     * @param ApiConfiguration $apiConfiguration
     * @param QueueAll         $queueAll
     * @param Log              $logger
     */
    public function __construct(
        Data $inventoryService,
        ApiConfiguration $apiConfiguration,
        QueueAll $queueAll,
        Log $logger
    ) {
        $this->inventoryService = $inventoryService;
        $this->apiConfiguration = $apiConfiguration;
        $this->queueAll = $queueAll;
        $this->logger = $logger;
    }

    public function updateStock()
    {
        if (!$this->apiConfiguration->canUpdateInventory()) {
            return;
        }

        $this->inventoryService->updateProductInventory();
    }

    public function forceUpdateStock()
    {
        if (!$this->apiConfiguration->canUpdateInventory()) {
            return;
        }

        $this->inventoryService->forceUpdateProductInventory();
    }

    public function forceStockQueue()
    {
        if (!$this->apiConfiguration->canUpdateInventory()) {
            return;
        }

        $queueingResult = $this->queueAll->queueAll();

        if ($queueingResult) {
            $this->logger->log('debug', 'Your products have been successfully queued and their stock will 
            be send to Vendiro soon.' . ' Depending on your amount of products, this may take a few minutes up to a
            few hours.');

            return;
        }

        $this->logger->log('debug', 'The products could not be queued for updating the stock at Vendiro.');
    }
}
