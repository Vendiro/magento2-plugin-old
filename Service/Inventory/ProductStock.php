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
namespace TIG\Vendiro\Service\Inventory;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use TIG\Vendiro\Model\Config\Provider\General\Configuration;
use TIG\Vendiro\Model\Config\Source\General\Inventory;
use TIG\Vendiro\Service\Inventory\ProductStock\NormalQty;
use TIG\Vendiro\Service\Inventory\ProductStock\SalableQty;
use TIG\Vendiro\Service\Software\Data as SoftwareData;

class ProductStock
{
    /** @var Configuration */
    private $configuration;

    /** @var SoftwareData */
    private $softwareData;

    /** @var NormalQty */
    private $normalQty;

    /** @var $salableQty */
    private $salableQty;

    /**
     * @param Configuration $configuration
     * @param SoftwareData  $softwareData
     * @param NormalQty     $normalQty
     * @param SalableQty    $salableQty
     */
    public function __construct(
        Configuration $configuration,
        SoftwareData $softwareData,
        NormalQty $normalQty,
        SalableQty $salableQty
    ) {
        $this->configuration = $configuration;
        $this->softwareData = $softwareData;
        $this->normalQty = $normalQty;
        $this->salableQty = $salableQty;
    }

    /**
     * @param string $sku
     *
     * @return float|int
     * @throws InputException
     * @throws LocalizedException
     */
    public function getStockBySku($sku)
    {
        $qty = 0;
        $inventoryType = $this->configuration->getInventoryQuantityType();

        if ($inventoryType == Inventory::INVENTORY_TYPE_SALABLE && $this->softwareData->getMagentoVersion() != '2.2') {
            $qty = $this->salableQty->getQtyBySku($sku);
        }

        if ($inventoryType == Inventory::INVENTORY_TYPE_REGULAR) {
            $qty = $this->normalQty->getQtyBySku($sku);
        }

        return $qty;
    }
}
