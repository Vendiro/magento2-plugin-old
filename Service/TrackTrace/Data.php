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

namespace TIG\Vendiro\Service\TrackTrace;

use Magento\Sales\Api\Data\ShipmentInterface;
use TIG\Vendiro\Api\Data\CarrierInterface;
use TIG\Vendiro\Api\Data\OrderInterface;
use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Service\Order\ApiStatusManager;
use TIG\Vendiro\Webservices\Endpoints\ConfirmShipment;

class Data
{
    /** @var ConfirmShipment */
    private $confirmShipment;

    /** @var CarrierInterface */
    private $carrierInterface;

    /** @var ApiConfiguration */
    private $apiConfiguration;

    /** @var ApiStatusManager */
    private $apiStatusManager;

    /**
     * @param ApiConfiguration        $apiConfiguration
     * @param ApiStatusManager        $apiStatusManager
     * @param CarrierInterface        $carrierInterface
     * @param ConfirmShipment         $confirmShipment
     */
    public function __construct(
        ApiConfiguration $apiConfiguration,
        ApiStatusManager $apiStatusManager,
        CarrierInterface $carrierInterface,
        ConfirmShipment $confirmShipment
    ) {
        $this->apiConfiguration = $apiConfiguration;
        $this->apiStatusManager = $apiStatusManager;
        $this->carrierInterface = $carrierInterface;
        $this->confirmShipment = $confirmShipment;
    }

    /**
     * @param OrderInterface $order
     * @param ShipmentInterface $shipment
     *
     * @return array|void
     */
    public function setShipment($order, $shipment)
    {
        if (!$this->apiConfiguration->canRegisterShipments()) {
            return;
        }

        $vendiroId = $order->getVendiroId();
        $carrierId = $this->carrierInterface->getCarrierId();
        $vendiroOrder = $this->apiStatusManager->getOrders($vendiroId);
        $shipmentCode = $vendiroOrder->getShipmentCode();
        $tracks = $shipment->getTracks();

        foreach ($tracks as $track) {
            $titles = $track->getTitle();
            $numbers = $track->getNumber();

            $result = array_merge($titles, $numbers);
        }

        $this->shipmentCall($vendiroOrder, $carrierId, $shipmentCode);
    }

    /**
     * @param $vendiroOrderId
     * @param $carrierId
     * @param $shipmentCode
     *
     * @return mixed
     */
    public function shipmentCall($vendiroOrderId, $carrierId, $shipmentCode)
    {
        $requestData = ['carrier_id' => $carrierId, 'shipment_code' => $shipmentCode];
        $this->confirmShipment->setRequestData($requestData);

        return $this->confirmShipment->call($vendiroOrderId);
    }
}
