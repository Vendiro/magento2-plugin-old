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

use TIG\Vendiro\Webservices\Endpoints\AcceptOrder;
use TIG\Vendiro\Webservices\Endpoints\GetOrders;
use TIG\Vendiro\Webservices\Endpoints\RejectOrder;

class ApiStatusManager
{
    /** @var GetOrders */
    private $getOrders;

    /** @var AcceptOrder */
    private $acceptOrder;

    /** @var RejectOrder */
    private $rejectOrder;

    /**
     * @param GetOrders   $getOrders
     * @param AcceptOrder $acceptOrder
     * @param RejectOrder $rejectOrder
     */
    public function __construct(
        GetOrders $getOrders,
        AcceptOrder $acceptOrder,
        RejectOrder $rejectOrder
    ) {
        $this->getOrders = $getOrders;
        $this->acceptOrder = $acceptOrder;
        $this->rejectOrder = $rejectOrder;
    }

    /**
     * @return array|mixed|\Zend_Http_Response
     */
    public function getOrders()
    {
        $requestData = ['order_status' => 'new','include_addresses' => 'true'];
        $this->getOrders->setRequestData($requestData);

        return $this->getOrders->call();
    }

    /**
     * @param int|string $vendiroOrderId
     * @param int|string $magentoOrderId
     *
     * @return array|mixed|\Zend_Http_Response
     */
    public function acceptOrder($vendiroOrderId, $magentoOrderId)
    {
        $requestData = ['order_ref' => $magentoOrderId];
        $this->acceptOrder->setRequestData($requestData);

        return $this->acceptOrder->call($vendiroOrderId);
    }

    /**
     * @param int|string $vendiroOrderId
     * @param string     $reason
     *
     * @return array|mixed|\Zend_Http_Response
     */
    public function rejectOrder($vendiroOrderId, $reason)
    {
        $requestData = ['reason' => $reason];
        $this->rejectOrder->setRequestData($requestData);

        return $this->rejectOrder->call($vendiroOrderId);
    }
}
