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
namespace TIG\Vendiro\Setup\V111\Schema;

use Magento\Framework\DB\Ddl\Table;
use TIG\Vendiro\Setup\AbstractColumnInstaller;

class UpgradeOrderTable extends AbstractColumnInstaller
{
    const TABLE_NAME = 'tig_vendiro_order';

    protected $columns = [
        'marketplace_orderid',
        'marketplace_name'
    ];

    /**
     * @return array
     */
    public function installMarketplaceOrderidColumn()
    {
        return [
            'type' => Table::TYPE_TEXT,
            'length' => 64,
            'nullable' => true,
            'default' => null,
            'comment' => 'Marketplace OrderId',
            'after' => 'vendiro_id',
        ];
    }

    /**
     * @return array
     */
    public function installMarketplaceNameColumn()
    {
        return [
            'type' => Table::TYPE_TEXT,
            'length' => 64,
            'nullable' => true,
            'default' => null,
            'comment' => 'Marketplace Name',
            'after' => 'marketplace_orderid',
        ];
    }
}
