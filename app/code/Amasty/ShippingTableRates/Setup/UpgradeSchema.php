<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade Schema scripts
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_table_method'),
                'free_types',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => '',
                    'length' => 255,
                    'comment' => 'Free Types'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->addAmmethodStoreTable($setup);
        }

        if (version_compare($context->getVersion(), '1.2.1', '<')) {
            $this->updateQtyToDecimal($setup);
        }

        if (version_compare($context->getVersion(), '1.2.2', '<')) {
            $this->addCommentImageColumn($setup);
        }

        if (version_compare($context->getVersion(), '1.4.2', '<')) {
            $this->addCity($setup);
        }

        if (version_compare($context->getVersion(), '1.4.3', '<')) {
            $this->updateCity($setup);
        }

        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->addName($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    private function addName(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('amasty_table_rate');
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $setup->getConnection();
        $connection->addColumn(
            $tableName,
            'name_delivery',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 255,
                'comment' => 'Name'
            ]
        );
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    private function updateCity(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('amasty_table_rate');
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $setup->getConnection();
        if ($connection->isTableExists($tableName)) {
            $connection->changeColumn(
                $tableName,
                'city',
                'city',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => false,
                    'length' => 255,
                    'comment' => 'City',
                    'default' => ''
                ]
            );
        }
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    private function addCity(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('amasty_table_rate');
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $setup->getConnection();
        $connection->addColumn(
            $tableName,
            'city',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 255,
                'comment' => 'City'
            ]
        );
    }

    private function updateQtyToDecimal(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('amasty_table_rate');
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $setup->getConnection();
        if ($connection->isTableExists($tableName)) {
            $connection->changeColumn(
                $tableName,
                'qty_from',
                'qty_from',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,2',
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '0.00'
                ]
            );

            $connection->changeColumn(
                $tableName,
                'qty_to',
                'qty_to',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,2',
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '0.00'
                ]
            );
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    private function addAmmethodStoreTable($setup)
    {
        /**
         * Create table 'amasty_method_store'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('amasty_method_label'))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'method_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Method Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Entity Id'
            )
            ->addColumn(
                'label',
                Table::TYPE_TEXT,
                '255',
                ['nullable' => true, 'default' => null],
                'Label'
            )
            ->addColumn(
                'comment',
                Table::TYPE_TEXT,
                '255',
                ['nullable' => true, 'default' => null],
                'Comment'
            )
            ->addForeignKey(
                $setup->getFkName('amasty_method_label', 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName('amasty_method_label', 'method_id', 'amasty_table_method', 'id'),
                'method_id',
                $setup->getTable('amasty_table_method'),
                'id',
                Table::ACTION_CASCADE
            );
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addCommentImageColumn(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $tableName = $setup->getTable('amasty_table_method');
        $connection->addColumn(
            $tableName,
            'comment_img',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 255,
                'comment' => 'Comment Image'
            ]
        );
    }
}
