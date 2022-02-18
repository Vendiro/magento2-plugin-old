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
namespace TIG\Vendiro\Setup\V132\Schema;

use TIG\Vendiro\Setup\AbstractTableInstaller;

class InstallInvoicesTable extends AbstractTableInstaller
{
    const TABLE_NAME = 'tig_vendiro_invoice';

    /**
     * @return void
     * @throws \Zend_Db_Exception
     */
    // @codingStandardsIgnoreLine
    protected function defineTable()
    {
        $columns = ['invoice_id'];

        $this->addEntityId();
        $this->addInt('invoice_id', 'invoice_id');
        $this->addIndex($columns);
        $this->addInt('order_id', 'Order Id', 64, false);
        $this->addInt('marketplace_id', 'Marketplace Id', 64, false);
        $this->addInt('marketplace_orderid', 'Marketplace Order Id', 64, false);
    }
}
