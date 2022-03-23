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
namespace TIG\Vendiro\Api\Data;

interface InvoiceInterface
{
    /**
     * @return int
     */
    public function getInvoiceId();

    /**
     * @param int $value
     *
     * @return InvoiceInterface
     */
    public function setInvoiceId();

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $value
     *
     * @return InvoiceInterface
     */
    public function setOrderId();

    /**
     * @return int
     */
    public function getMarketplaceId();
    /**
     * @param int $value
     *
     * @return InvoiceInterface
     */
    public function setMarketplaceId();

    /**
     * @return int
     */
    public function getMarketplaceOrderId();

    /**
     * @param int $value
     *
     * @return InvoiceInterface
     */
    public function setMarketplaceOrderId();
}
