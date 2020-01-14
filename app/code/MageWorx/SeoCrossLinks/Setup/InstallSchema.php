<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'mageworx_seocrosslinks_crosslink'
         */
        $tableCrosslink = $installer->getConnection()->newTable(
            $installer->getTable('mageworx_seocrosslinks_crosslink')
        )->addColumn(
            'crosslink_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                ],
            'CrossLink ID'
        )->addColumn(
            'keyword',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [
                'nullable' => false,
                ],
            'Keyword'
        )->addColumn(
            'link_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [
                'nullable' => false,
                ],
            'Title Link'
        )->addColumn(
            'link_target',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default' => 1,
                ],
            'Target Link'
        )->addColumn(
            'replacement_count',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default' => 1,
                ],
            'Count of Replacements'
        )->addColumn(
            'ref_static_url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [
                'nullable' => true,
                ],
            'Reference by Custom URL'
        )->addColumn(
            'ref_product_sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [
                'nullable' => true,
                ],
            'Reference by Product SKU'
        )->addColumn(
            'ref_category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => true,
                ],
            'Reference by Category ID'
        )->addColumn(
            'in_product',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default' => 1,
                ],
            'Use in Product Page'
        )->addColumn(
            'in_category',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default' => 1,
                ],
            'Use in Category Page'
        )->addColumn(
            'in_cms_page',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default' => 1,
                ],
            'Use in CMS Page'
        )->addColumn(
            'priority',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default' => 0,
                ],
            'Priority'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default' => 1,
                ],
            'Is Active'
        );
        $installer->getConnection()->createTable($tableCrosslink);

        /**
         * Create table 'mageworx_seocrosslinks_crosslink_store'
         */
        $tableCrosslinkStore = $installer->getConnection()->newTable(
            $installer->getTable('mageworx_seocrosslinks_crosslink_store')
        )->addColumn(
            'crosslink_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                ],
            'Cross Link ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                ],
            'Store ID'
        )->addForeignKey(
            $installer->getFkName(
                'mageworx_seocrosslinks_crosslink_store',
                'crosslink_id',
                'mageworx_seocrosslinks_crosslink',
                'crosslink_id'
            ),
            'crosslink_id',
            $installer->getTable('mageworx_seocrosslinks_crosslink'),
            'crosslink_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'mageworx_seocrosslinks_crosslink_store',
                'store_id',
                'store',
                'store_id'
            ),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Crosslink To Store Linkage Table'
        );
        $installer->getConnection()->createTable($tableCrosslinkStore);

        $installer->endSetup();
    }
}
