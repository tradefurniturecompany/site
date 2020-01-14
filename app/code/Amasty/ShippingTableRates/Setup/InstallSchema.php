<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Install schema script
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $tableMethod  = $installer->getConnection()
            ->newTable($installer->getTable('amasty_table_method'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                8,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Is Active'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Name'
            )
            ->addColumn(
                'comment',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Comment'
            )
            ->addColumn(
                'stores',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Stores'
            )
            ->addColumn(
                'cust_groups',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Groups'
            )
            ->addColumn(
                'select_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                2,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Select Rate'
            )
            ->addColumn(
                'min_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,2',
                ['nullable' => false, 'unsigned' => true, 'default' => 0,00],
                'Min Rate'
            )
            ->addColumn(
                'max_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,2',
                ['nullable' => false, 'unsigned' => true, 'default' => 0,00],
                'Max Rate'
            )
        ;

        $tableRate  = $installer->getConnection()
            ->newTable($installer->getTable('amasty_table_rate'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'method_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                8,
                ['unsigned' => true, 'nullable' => false],
                'Method ID'
            )
            ->addColumn(
                'country',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                4,
                ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'nullable' => false],
                'Country'
            )
            ->addColumn(
                'state',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'State'
            )
            ->addColumn(
                'zip_from',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                10,
                ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'nullable' => false],
                'ZIP From'
            )
            ->addColumn(
                'zip_to',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                10,
                ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'nullable' => false],
                'ZIP To'
            )
            ->addColumn(
                'price_from',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,2',
                ['nullable' => false, 'unsigned' => true, 'default' => 0,00],
                'Price From'
            )
            ->addColumn(
                'price_to',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,2',
                ['nullable' => false, 'unsigned' => true, 'default' => 0,00],
                'Price to'
            )
            ->addColumn(
                'weight_from',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'unsigned' => true, 'default' => 0,0000],
                'Weight From'
            )
            ->addColumn(
                'weight_to',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'unsigned' => true, 'default' => 0,0000],
                'Weight to'
            )
            ->addColumn(
                'qty_from',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'QTY From'
            )
            ->addColumn(
                'qty_to',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'QTY To'
            )
            ->addColumn(
                'shipping_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Shipping Type'
            )
            ->addColumn(
                'cost_base',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,2',
                ['nullable' => false, 'unsigned' => true, 'default' => 0,00],
                'Cost Base'
            )
            ->addColumn(
                'cost_percent',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '5,2',
                ['nullable' => false, 'unsigned' => true, 'default' => 0,00],
                'Cost Percent'
            )
            ->addColumn(
                'cost_product',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,2',
                ['nullable' => false, 'unsigned' => true, 'default' => 0,00],
                'Cost Product'
            )
            ->addColumn(
                'cost_weight',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,2',
                ['nullable' => false, 'unsigned' => true, 'default' => 0,00],
                'Cost Weight'
            )
            ->addColumn(
                'time_delivery',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'default' => null],
                'Time Delivery'
            )
            ->addColumn(
                'num_zip_from',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'default' => null],
                'Num Zip To'
            )
            ->addColumn(
                'num_zip_to',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'default' => null],
                'Num Zip  To'
            )
            ->addIndex('idx_amasty_table_rate_method_id', 'method_id')
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_table_rate',
                    'method_id',
                    'amasty_table_method',
                    'id'
                ),
                'method_id',
                $installer->getTable('amasty_table_method'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

        $installer->getConnection()->createTable($tableMethod);
        $installer->getConnection()->createTable($tableRate);
        $installer->endSetup();
    }
}
