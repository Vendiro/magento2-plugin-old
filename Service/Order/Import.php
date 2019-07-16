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
namespace TIG\Vendiro\Service\Order;

use TIG\Vendiro\Api\OrderRepositoryInterface;
use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Webservices\Endpoints\GetOrders;

class Import
{
    /** @var ApiConfiguration */
    private $apiConfiguration;

    /** @var GetOrders */
    private $getOrder;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /**
     * @param ApiConfiguration         $apiConfiguration
     * @param GetOrders                $getOrder
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        ApiConfiguration $apiConfiguration,
        GetOrders $getOrder,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->apiConfiguration = $apiConfiguration;
        $this->getOrder = $getOrder;
        $this->orderRepository = $orderRepository;
    }

    public function execute()
    {
        if (!$this->apiConfiguration->canImportOrders()) {
            return;
        }

        $orders = $this->orderRepository->getByStatus('New - Validated');

        /** @var \TIG\Vendiro\Api\Data\OrderInterface $order */
        foreach ($orders as $order) {
            $apiOrderDetails = $this->getOrder->call($order->getVendiroId());

            //TODO: Import order details into Magento
            //TODO: Vendiro Accept API call if success
            //TODO: Vendiro Reject API call if failure
        }
    }
}
