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

namespace TIG\Vendiro\Service\Carrier;

use TIG\Vendiro\Api\CarrierRepositoryInterface;
use TIG\Vendiro\Exception;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\CarrierRepository;
use TIG\Vendiro\Webservices\Endpoints\GetCarriers;

class Data
{
    /** @var Log $logger */
    private $logger;

    /** @var GetCarriers $getCarriers */
    private $getCarriers;

    /** @var CarrierRepositoryInterface $carrierRepository */
    private $carrierRepositoryInterface;

    /** @var CarrierRepository */
    private $carrierRepository;

    /**
     * Data constructor.
     *
     * @param Log                                         $logger
     * @param GetCarriers                                 $getCarriers
     * @param CarrierRepositoryInterface                  $carrierRepositoryInterface
     * @param CarrierRepository                           $carrierRepository
     */
    public function __construct(
        Log $logger,
        GetCarriers $getCarriers,
        CarrierRepositoryInterface $carrierRepositoryInterface,
        CarrierRepository $carrierRepository
    ) {
        $this->logger                     = $logger;
        $this->getCarriers                = $getCarriers;
        $this->carrierRepositoryInterface = $carrierRepositoryInterface;
        $this->carrierRepository          = $carrierRepository;
    }

    /**
     * @throws \TIG\Vendiro\Exception
     */
    public function updateCarriers()
    {
        $carriers = $this->getCarriers->call();

        if (array_key_exists('message', $carriers)) {
            return false;
        }

        $carrierIds = [];
        array_push($carrierIds, array_keys($carriers));
        $duplicateCarriers = $this->carrierRepository->getByFieldWithValue('carrier_id', $carrierIds, 0, 'in');

        foreach ($duplicateCarriers as $duplicateCarrier) {
            $this->updateCarrier($duplicateCarrier, $carriers[$duplicateCarrier->getCarrierId()]);
            unset($carriers[$duplicateCarrier->getCarrierId()]);
        }

        foreach ($carriers as $carrierId => $carrier) {
            $this->saveCarrier($carrierId, $carrier);
        }
    }

    /**
     * @param $carrierId
     * @param $carrier
     *
     * @throws \TIG\Vendiro\Exception
     */
    private function saveCarrier($carrierId, $carrier)
    {
        try {
            $carrierModel = $this->carrierRepositoryInterface->create();
            $carrierModel->setCarrierId($carrierId);
            $carrierModel->setCarrier($carrier);
            $this->carrierRepositoryInterface->save($carrierModel);
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new Exception(__($exception->getMessage()));
        }
    }

    public function updateCarrier($duplicateCarrier, $carrier)
    {
        if ($duplicateCarrier->getCarrier() != $carrier) {
            $duplicateCarrier->setCarrier($carrier);
            $duplicateCarrier->save();
        }
    }
}
