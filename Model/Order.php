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
use TIG\Vendiro\Api\Data\OrderInterface;

// @codingStandardsIgnoreFile

/**
 * Class Order - Ignore File because it mainly contains getters and setters
 *
 * @package TIG\Vendiro\Model
 */
class Order extends AbstractModel implements OrderInterface
{
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_VENDIRO_ID = 'vendiro_id';
    const FIELD_MARKETPLACE_ORDERID = 'marketplace_orderid';
    const FIELD_MARKETPLACE_NAME = 'marketplace_name';
    const FIELD_MARKETPLACE_REFERENCE = 'marketplace_reference';
    const FIELD_STATUS = 'status';
    const FIELD_CREATED_AT = 'created_at';

    protected function _construct()
    {
        $this->_init('TIG\Vendiro\Model\ResourceModel\Order');
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(self::FIELD_ORDER_ID);
    }

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setOrderId($value)
    {
        return $this->setData(self::FIELD_ORDER_ID, $value);
    }

    /**
     * @return int
     */
    public function getVendiroId()
    {
        return $this->getData(self::FIELD_VENDIRO_ID);
    }

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setVendiroId($value)
    {
        return $this->setData(self::FIELD_VENDIRO_ID, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getMarketplaceOrderid()
    {
        return $this->getData(self::FIELD_MARKETPLACE_ORDERID);
    }

    /**
     * {@inheritDoc}
     */
    public function setMarketplaceOrderid($value)
    {
        return $this->setData(self::FIELD_MARKETPLACE_ORDERID, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getMarketplaceName()
    {
        return $this->getData(self::FIELD_MARKETPLACE_NAME);
    }

    /**
     * {@inheritDoc}
     */
    public function setMarketplaceName($value)
    {
        return $this->setData(self::FIELD_MARKETPLACE_NAME, $value);
    }

    /**
     * @return int
     */
    public function getMarketplaceReference()
    {
        return $this->getData(self::FIELD_MARKETPLACE_REFERENCE);
    }

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setMarketplaceReference($value)
    {
        return $this->setData(self::FIELD_MARKETPLACE_REFERENCE, $value);
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
     * @return \TIG\Vendiro\Api\Data\OrderInterface
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
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::FIELD_CREATED_AT, $value);
    }
}
