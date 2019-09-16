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
 * to servicedesk@tig.nl so we can send you a copy immediately.
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

namespace TIG\Vendiro\Model\Config\Provider\General;

use TIG\Vendiro\Model\AbstractConfigProvider;

class Configuration extends AbstractConfigProvider
{
    const VENDIRO_GENERAL_MODE = 'tig_vendiro/general/mode';

    const VENDIRO_VENDIRO_KEY   = 'tig_vendiro/vendiro/key';
    const VENDIRO_VENDIRO_TOKEN = 'tig_vendiro/vendiro/token';

    const VENDIRO_ORDER_IMPORT_ENABLED      = 'tig_vendiro/vendiro/orders';
    const VENDIRO_REGISTER_SHIPMENT_ENABLED = 'tig_vendiro/vendiro/shipment';
    const VENDIRO_UPDATE_INVENTORY_ENABLED  = 'tig_vendiro/vendiro/inventory';
    const VENDIRO_INVENTORY_QUANTITY_TYPE   = 'tig_vendiro/vendiro/inventory_quantity';
    const VENDIRO_DEFAULT_SHIPPING_METHOD = 'tig_vendiro/vendiro/default_shipment_method';

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getMode($store = null)
    {
        return $this->getConfigValue(static::VENDIRO_GENERAL_MODE, $store);
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function liveModeEnabled($store = null)
    {
        if ($this->getMode($store) == 1) {
            return true;
        }

        return false;
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function testModeEnabled($store = null)
    {
        if ($this->getMode($store) == 2) {
            return true;
        }

        return false;
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isEnabled($store = null)
    {
        if ($this->testModeEnabled($store) || $this->liveModeEnabled($store)) {
            return true;
        }

        return false;
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getKey($store = null)
    {
        return $this->getConfigValue(static::VENDIRO_VENDIRO_KEY, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getToken($store = null)
    {
        return $this->getConfigValue(static::VENDIRO_VENDIRO_TOKEN, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function isOrderImportEnabled($store = null)
    {
        return $this->getConfigValue(static::VENDIRO_ORDER_IMPORT_ENABLED, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function isRegisterShipmentEnabled($store = null)
    {
        return $this->getConfigValue(static::VENDIRO_REGISTER_SHIPMENT_ENABLED, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function isUpdateInventoryEnabled($store = null)
    {
        return $this->getConfigValue(static::VENDIRO_UPDATE_INVENTORY_ENABLED, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getInventoryQuantityType($store = null)
    {
        return $this->getConfigValue(static::VENDIRO_INVENTORY_QUANTITY_TYPE, $store);
    }

    /**
     * @param $store
     *
     * @return int
     */
    public function getDefaultCarrier($store = null)
    {
        return $this->getConfigValue(static::VENDIRO_DEFAULT_SHIPPING_METHOD, $store);
    }
}
