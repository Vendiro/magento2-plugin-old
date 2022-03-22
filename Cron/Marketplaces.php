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

use TIG\Vendiro\Model\Config\Provider\General\Configuration;
use TIG\Vendiro\Service\Marketplaces\Data as MarketplaceService;

class Marketplaces
{
    /** @var Configuration $configuration */
    private $configuration;

    /** @var MarketplaceService $marketplaceService */
    private $marketplaceService;

    /**
     * Carrier constructor.
     *
     * @param Configuration  $configuration
     * @param MarketplaceService $marketplaceService
     */
    public function __construct(
        Configuration $configuration,
        MarketplaceService $marketplaceService
    ) {
        $this->configuration = $configuration;
        $this->marketplaceService = $marketplaceService;
    }

    public function updateMarketplaces()
    {
        if (!$this->configuration->isEnabled()) {
            return;
        }

        $this->marketplaceService->updateMarketplaces();
    }
}
