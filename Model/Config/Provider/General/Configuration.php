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

    const VENDIRO_VENDIRO_ORDERS             = 'tig_vendiro/vendiro/orders';
    const VENDIRO_VENDIRO_SHIPMENT           = 'tig_vendiro/vendiro/shipment';
    const VENDIRO_VENDIRO_INVENTORY          = 'tig_vendiro/vendiro/inventory';
    const VENDIRO_VENDIRO_INVENTORY_QUANTITY = 'tig_vendiro/vendiro/inventory_quantity';

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
    public function getOrders($store = null)
    {
        return $this->getConfigValue(static::VENDIRO_VENDIRO_ORDERS, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getShipment($store = null)
    {
        return $this->getConfigValue(static::VENDIRO_VENDIRO_SHIPMENT, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getInventory($store = null)
    {
        return $this->getConfigValue(static::VENDIRO_VENDIRO_INVENTORY, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getInventoryQuantity($store = null)
    {
        return $this->getConfigValue(static::VENDIRO_VENDIRO_INVENTORY_QUANTITY, $store);
    }
}
