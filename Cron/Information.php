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
namespace TIG\Vendiro\Cron;

use TIG\Vendiro\Model\Config\Provider\General\Configuration;
use TIG\Vendiro\Service\Carrier\Data as CarrierService;
use TIG\Vendiro\Service\Marketplaces\Data as MarketplaceService;

class Information
{
    /** @var Configuration $configuration */
    private $configuration;

    /** @var CarrierService $carrierService */
    private $carrierService;

    /** @var MarketplaceService */
    private $marketplaceService;

    /**
     * @param Configuration      $configuration
     * @param CarrierService     $carrierService
     * @param MarketplaceService $marketplaceService
     */
    public function __construct(
        Configuration $configuration,
        CarrierService $carrierService,
        MarketplaceService $marketplaceService
    ) {
        $this->configuration = $configuration;
        $this->carrierService = $carrierService;
        $this->marketplaceService = $marketplaceService;
    }

    public function updateInformation()
    {
        if (!$this->configuration->isEnabled()) {
            return;
        }

        $this->carrierService->updateCarriers();
        $this->marketplaceService->updateMarketplaces();
    }
}
