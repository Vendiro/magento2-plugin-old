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

use Magento\Framework\Api\SearchCriteriaBuilder;
use TIG\Vendiro\Webservices\Endpoints\GetOrders;
use TIG\Vendiro\Api\OrderRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Data
{
    /** @var GetOrders $getOrders */
    private $getOrders;

    /** @var OrderRepositoryInterface $orderRepository */
    private $orderRepository;

    /** @var DateTime $date */
    private $date;

    /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /**
     * Data constructor.
     *
     * @param \TIG\Vendiro\Webservices\Endpoints\GetOrders $getOrders
     * @param \TIG\Vendiro\Api\OrderRepositoryInterface    $orderRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime  $date
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        GetOrders $getOrders,
        OrderRepositoryInterface $orderRepository,
        DateTime $date,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->getOrders = $getOrders;
        $this->orderRepository = $orderRepository;
        $this->date = $date;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function saveOrders()
    {
        $results = $this->getOrders->call();

        $valuesToSkip = $this->getAlreadyInsertedOrders($results);

        foreach ($results as $order) {
            if (!in_array($order['id'], $valuesToSkip)) {
                continue;
            }

            $vendiroOrder = $this->orderRepository->create();
            $vendiroOrder->setVendiroId($order['id']);
            $vendiroOrder->setOrderRef($order['order_ref']);
            $vendiroOrder->setMarketplaceOrderId($order['marketplace_order_id']);
            $vendiroOrder->setOrderDate($order['date_order']);
            $vendiroOrder->setFulfilmentByMarketplace($order['fulfilment_by_marketplace']);
            $vendiroOrder->setCreatedAt($order['created']);
            $vendiroOrder->setMarketplaceName($order['marketplace']['name']);
            $vendiroOrder->setMarketplaceReference($order['marketplace']['reference']);
            $vendiroOrder->setStatus($order['status']['name']);
            $vendiroOrder->setImportedAt($this->date->gmtDate());

            try {
                $vendiroOrder->save();
            } catch (\Exception $exception) {

            }
        }

        return;
    }

    public function getAlreadyInsertedOrders($results)
    {
        $orderIds = array_column($results, 'id');
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('vendiro_id', $orderIds, 'in');

        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->orderRepository->getList($searchCriteria->create());

        $valuesToSkip = array_diff($orderIds, array_keys($list->getItems()));

        return $valuesToSkip;
    }

    public function getOrdersToProcess()
    {

    }
}
