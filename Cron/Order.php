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
use TIG\Vendiro\Service\Order\Data;
use TIG\Vendiro\Service\Order\Import;

class Order
{
    /** @var Data $orderService */
    private $orderService;

    /** @var Import $orderImportService */
    private $orderImportService;

    /** @var ApiConfiguration */
    private $apiConfiguration;

    /**
     * @param Data             $orderService
     * @param Import           $orderImportService
     * @param ApiConfiguration $apiConfiguration
     */
    public function __construct(
        Data $orderService,
        Import $orderImportService,
        ApiConfiguration $apiConfiguration
    ) {
        $this->orderService = $orderService;
        $this->orderImportService = $orderImportService;
        $this->apiConfiguration = $apiConfiguration;
    }

    public function retrieveNewOrders()
    {
        if (!$this->apiConfiguration->canImportOrders()) {
            return;
        }

        $this->orderService->saveOrders();
    }

    public function importToMagento()
    {
        if (!$this->apiConfiguration->canImportOrders()) {
            return;
        }

        $this->orderImportService->importToMagento();
    }
}
