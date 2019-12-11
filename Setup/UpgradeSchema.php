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
namespace TIG\Vendiro\Setup;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /** @var array */
    private $upgradeSchemaObjects;

    /**
     * @param array $upgradeSchemaObjects
     */
    public function __construct(
        $upgradeSchemaObjects = []
    ) {
        $this->upgradeSchemaObjects = $upgradeSchemaObjects;
    }

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws LocalizedException
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $tableName = $setup->getTable('sales_shipment');
            $connection = $setup->getConnection();
            $column = [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Vendiro Carrier',
                    'default' => null
                ];
            $connection->addColumn($tableName, 'vendiro_carrier', $column);
        }

        foreach ($this->upgradeSchemaObjects as $version => $schemaObjects) {
            $this->upgradeSchemas($version, $schemaObjects, $setup, $context);
        }

        $setup->endSetup();
    }

    /**
     * @param                        $version
     * @param array                  $schemaObjects
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws LocalizedException
     */
    private function upgradeSchemas($version, $schemaObjects, $setup, $context)
    {
        $version = str_replace('v', '', $version);

        if (!version_compare($context->getVersion(), $version, '<')) {
            return;
        }

        /** @var AbstractColumnInstaller $schema */
        foreach ($schemaObjects as $schema) {
            $schema->install($setup, $context);
        }
    }
}
