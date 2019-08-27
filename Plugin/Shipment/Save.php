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

use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use TIG\Vendiro\Model\Config\Provider\General\CarrierConfiguration;
use Magento\Framework\Message\ManagerInterface;
use TIG\Vendiro\Exception;

class Save
{
    /** @var ManagerInterface */
    private $managerInterface;

    /**
     * @var RequestInterface $request
     */
    private $request;

    /**
     * @var CarrierConfiguration $configuration
     */
    private $configuration;

    public function __construct(
        RequestInterface $request,
        CarrierConfiguration $configuration,
        ManagerInterface $managerInterface
    ) {
        $this->request = $request;
        $this->configuration = $configuration;
        $this->managerInterface = $managerInterface;
    }

    /**
     * @param $subject
     *
     * @return ShipmentInterface
     */
    public function beforeSave($subject)
    {
        $order = $subject->getOrder();
        if ($order->getShippingMethod() != 'tig_vendiro_shipping') {
            return $subject;
        }

        return $this->saveVendiroCarrier($subject);
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return ShipmentInterface
     * @throws \TIG\Vendiro\Exception
     */
    private function saveVendiroCarrier(ShipmentInterface $shipment)
    {
        $vendiroCarrier = $this->request->getParam('shipment')['tig_vendiro_carrier'];

        if ($vendiroCarrier) {
            $shipment->setVendiroCarrier($vendiroCarrier);
        }

        if (!$shipment->getVendiroCarrier()) {
            $defaultCarrier = $this->configuration->getDefaultCarrier($shipment->getStoreId());
            $shipment->setVendiroCarrier($defaultCarrier);
        }

        if ($shipment->getVendiroCarrier() === '0') {
            $errorMessage = __(
                "Please select a default shipping method or select one below."
            );
            throw new Exception($errorMessage);
        }

        return $shipment;
    }
}
