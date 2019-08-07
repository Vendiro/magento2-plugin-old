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

namespace TIG\Vendiro\Service\Order;

use TIG\Vendiro\Api\Data\OrderInterface;
use TIG\Vendiro\Api\OrderRepositoryInterface;
use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Model\Config\Provider\QueueStatus;
use TIG\Vendiro\Webservices\Endpoints\GetOrders;
use TIG\Vendiro\Logging\Log;

class Data
{
    /** @var ApiConfiguration */
    private $apiConfiguration;

    /** @var GetOrders $getOrders */
    private $getOrders;

    /** @var OrderRepositoryInterface $orderRepository */
    private $orderRepository;

    /** @var Log $logger */
    private $logger;

    /**
     * Data constructor.
     *
     * @param ApiConfiguration         $apiConfiguration
     * @param GetOrders                $getOrders
     * @param OrderRepositoryInterface $orderRepository
     * @param Log                      $logger
     */
    public function __construct(
        ApiConfiguration $apiConfiguration,
        GetOrders $getOrders,
        OrderRepositoryInterface $orderRepository,
        Log $logger
    ) {
        $this->apiConfiguration = $apiConfiguration;
        $this->getOrders = $getOrders;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * @param $order
     *
     * @return OrderInterface
     */
    private function createVendiroOrder($order)
    {
        $vendiroOrder = $this->orderRepository->create();
        $vendiroOrder->setVendiroId($order['id']);
        $vendiroOrder->setMarketplaceReference($order['marketplace']['reference']);
        $vendiroOrder->setStatus(QueueStatus::QUEUE_STATUS_NEW);

        return $vendiroOrder;
    }

    public function saveOrders()
    {
        if (!$this->apiConfiguration->canImportOrders()) {
            return;
        }

        $results = $this->getOrders->call();

        $orderIds = array_column($results, 'id');
        $valuesToSkip = $this->orderRepository->getAlreadyInsertedOrders($orderIds);

        foreach ($results as $order) {
            $this->saveVendiroOrder($order, $valuesToSkip);
        }
    }

    /**
     * @param $order
     * @param $valuesToSkip
     */
    private function saveVendiroOrder($order, $valuesToSkip)
    {
        if (in_array($order['id'], $valuesToSkip)) {
            return;
        }

        $vendiroOrder = $this->createVendiroOrder($order);

        try {
            $this->orderRepository->save($vendiroOrder);
        } catch (\Exception $exception) {
            $errorMessage = 'Import on Vendiro order #' . $order['id'] . ' went wrong: ' . $exception->getMessage();
            $this->logger->critical($errorMessage);
        }
    }
}
