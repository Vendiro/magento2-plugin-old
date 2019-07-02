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
    
    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->getConfigValue(static::VENDIRO_GENERAL_MODE);
    }
    
    /**
     * @return bool
     */
    public function liveModeEnabled()
    {
        if ($this->getMode() == 1) {
            return true;
        }
        
        return false;
    }
    
    /**
     * @return bool
     */
    public function testModeEnabled()
    {
        if ($this->getMode() == 2) {
            return true;
        }
        
        return false;
    }
    
    /**
     * @return bool
     */
    public function isEnabled()
    {
        if ($this->testModeEnabled() || $this->liveModeEnabled()) {
            return true;
        }
        
        return false;
    }
}
