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
use TIG\Vendiro\Api\Data\TrackQueueInterface;

class TrackQueue extends AbstractModel implements TrackQueueInterface
{
    const FIELD_TRACK_ID        = 'track_id';
    const FIELD_STATUS          = 'status';
    const FIELD_CREATED_AT      = 'created_at';

    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('TIG\Vendiro\Model\ResourceModel\TrackQueue');
    }

    /**
     * @return int
     */
    public function getTrackId()
    {
        return $this->getData(self::FIELD_TRACK_ID);
    }

    /**
     * @param $value
     *
     * @return TrackQueueInterface
     */
    public function setTrackId($value)
    {
        return $this->setData(self::FIELD_TRACK_ID, $value);
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(self::FIELD_STATUS);
    }

    /**
     * @param $value
     *
     * @return TrackQueueInterface
     */
    public function setStatus($value)
    {
        return $this->setData(self::FIELD_STATUS, $value);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::FIELD_CREATED_AT);
    }

    /**
     * @param $value
     *
     * @return TrackQueueInterface
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::FIELD_CREATED_AT, $value);
    }
}
