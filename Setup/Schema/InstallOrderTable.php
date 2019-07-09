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
namespace TIG\Vendiro\Setup\Schema;

use TIG\Vendiro\Setup\AbstractTableInstaller;

class InstallOrderTable extends AbstractTableInstaller
{
    const TABLE_NAME = 'tig_vendiro_order';

    /**
     * @return void
     * @codingStandardsIgnoreLine
     */
    // @codingStandardsIgnoreLine
    protected function defineTable()
    {
        $this->addEntityId();
        $this->addInt('order_id', 'Order ID');
        $this->addInt('vendiro_id', 'Vendiro ID');
        $this->addInt('order_ref', 'Order reference');
        $this->addInt('marketplace_order_id', 'Marketplace order ID');
        $this->addTimestamp('order_date', 'Order date', false, '00-00-0000 00:00:00');
        $this->addBoolean('fulfilment_by_marketplace', 'Fulfilment by marketplace');
        $this->addTimestamp('created_at', 'Created at', false, '00-00-0000 00:00:00');
        $this->addText('marketplace_name', 'Marketplace name', 32, false);
        $this->addText('marketplace_reference', 'Marketplace reference', 32, false);
        $this->addText('status', 'Status', 32, false);
        $this->addTimestamp('imported_at', 'Imported at', false, '00-00-0000 00:00:00');
    }
}
