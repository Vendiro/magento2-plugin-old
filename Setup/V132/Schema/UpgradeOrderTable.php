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
namespace TIG\Vendiro\Setup\V132\Schema;

use Magento\Framework\DB\Ddl\Table;
use TIG\Vendiro\Setup\AbstractColumnInstaller;

class UpgradeOrderTable extends AbstractColumnInstaller
{
    const TABLE_NAME = 'tig_vendiro_order';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'marketplace_id',
        'vendiro_invoice_id'
    ];

    /**
     * @return array
     */
    public function installMarketplaceIdColumn()
    {
        return [
            'type' => Table::TYPE_INTEGER,
            'nullable' => true,
            'default' => null,
            'comment' => 'Vendiro Marketplace Id',
            'after' => 'status',
        ];
    }

    /**
     * @return array
     */
    public function installVendiroInvoiceIdColumn()
    {
        return [
            'type' => Table::TYPE_INTEGER,
            'nullable' => true,
            'default' => null,
            'comment' => 'Vendiro Invoice ID',
            'after' => 'marketplace_id',
        ];
    }
}
