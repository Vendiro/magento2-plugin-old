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
namespace TIG\Vendiro\Model;

use Magento\Framework\Model\AbstractModel;
use TIG\Vendiro\Api\Data\CarrierInterface;

class Carrier extends AbstractModel implements CarrierInterface
{
    const FIELD_CARRIER_ID = 'carrier_id';
    const FIELD_CARRIER    = 'carrier';

    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('TIG\Vendiro\Model\ResourceModel\Carrier');
    }

    /**
     * @return int
     */
    public function getCarrierId()
    {
        return $this->getData(self::FIELD_CARRIER_ID);
    }

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\CarrierInterface
     */
    public function setCarrierId($value)
    {
        return $this->setData(self::FIELD_CARRIER_ID, $value);
    }

    /**
     * @return int
     */
    public function getCarrier()
    {
        return $this->getData(self::FIELD_CARRIER);
    }

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\CarrierInterface
     */
    public function setCarrier($value)
    {
        return $this->setData(self::FIELD_CARRIER, $value);
    }
}
