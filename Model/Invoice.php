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
use TIG\Vendiro\Api\Data\InvoiceInterface;

// @codingStandardsIgnoreFile

/**
 * Class Order - Ignore File because it mainly contains getters and setters
 *
 * @package TIG\Vendiro\Model
 */
class Invoice extends AbstractModel implements InvoiceInterface
{
    const FIELD_INVOICE_ID = 'invoice_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_MARKETPLACE_ID = 'marketplace_id';
    const FIELD_MARKETPLACE_ORDER_ID = 'marketplace_orderid';

    protected function _construct()
    {
        $this->_init('TIG\Vendiro\Model\ResourceModel\Invoice');
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
     * @return Invoice
     */
    public function setOrderId($value)
    {
        return $this->setData(self::FIELD_ORDER_ID, $value);
    }

    /**
     * @return int
     */
    public function getMarketplaceId()
    {
        return $this->getData(self::FIELD_MARKETPLACE_ID);
    }

    /**
     * @param $value
     *
     * @return Invoice
     */
    public function setMarketplaceId($value)
    {
        return $this->setData(self::FIELD_MARKETPLACE_ID, $value);
    }

    /**
     * @return int
     */
    public function getMarketplaceOrderId()
    {
        return $this->getData(self::FIELD_MARKETPLACE_ORDER_ID);
    }

    /**
     * @param $value
     *
     * @return Invoice
     */
    public function setMarketplaceOrderId($value)
    {
        return $this->setData(self::FIELD_MARKETPLACE_ORDER_ID, $value);
    }

    /**
     * @return int
     */
    public function getInvoiceId()
    {
        return $this->getData(self::FIELD_INVOICE_ID);
    }

    /**
     * @param $value
     *
     * @return Invoice
     */
    public function setInvoiceId($value)
    {
        return $this->setData(self::FIELD_INVOICE_ID, $value);
    }
}
