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
namespace TIG\Vendiro\Model;

use Magento\Framework\Model\AbstractModel;
use TIG\Vendiro\Api\Data\StockInterface;

/**
 * @package TIG\Vendiro\Model
 */
class Stock extends AbstractModel implements StockInterface
{
    const FIELD_PRODUCT_SKU = 'product_sku';
    const FIELD_STATUS = 'status';
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_UPDATED_AT = 'updated_at';

    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('TIG\Vendiro\Model\ResourceModel\Stock');
    }

    /**
     * {@inheritDoc}
     */
    public function getProductSku()
    {
        return $this->getData(self::FIELD_PRODUCT_SKU);
    }

    /**
     * {@inheritDoc}
     */
    public function setProductSku($sku)
    {
        return $this->setData(self::FIELD_PRODUCT_SKU, $sku);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {
        return $this->getData(self::FIELD_STATUS);
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::FIELD_STATUS, $status);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::FIELD_CREATED_AT);
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::FIELD_CREATED_AT, $createdAt);
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::FIELD_UPDATED_AT);
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::FIELD_UPDATED_AT, $updatedAt);
    }
}
