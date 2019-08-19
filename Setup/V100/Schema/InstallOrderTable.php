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
namespace TIG\Vendiro\Setup\V100\Schema;

use Magento\Framework\DB\Ddl\Table;
use TIG\Vendiro\Setup\AbstractTableInstaller;

class InstallOrderTable extends AbstractTableInstaller
{
    const TABLE_NAME = 'tig_vendiro_order';

    /**
     * @return void
     * @throws \Zend_Db_Exception
     * @codingStandardsIgnoreLine
     */
    // @codingStandardsIgnoreLine
    protected function defineTable()
    {
        $this->addEntityId();
        $this->addInt('order_id', 'Order ID');
        $this->addInt('vendiro_id', 'Vendiro ID');
        $this->addText('marketplace_reference', 'Marketplace reference', 32, false);
        $this->addText('status', 'Status', 32, false);
        $this->addTimestamp('created_at', 'Created at', false, Table::TIMESTAMP_INIT);
        $this->addIndex('vendiro_id');
    }
}
