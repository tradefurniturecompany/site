<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Setup\InstallSchema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;

class AddMenuTables
{
    /**
     * Create Mega Menu Tables
     *
     * @param SchemaSetupInterface $installer
     */
    public function execute(SchemaSetupInterface $installer)
    {
        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable(LinkInterface::TABLE_NAME)
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Amasty Menu Link ID'
        )->addColumn(
            'link',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Amasty Menu Link Url'
        )->addIndex(
            $installer->getIdxName(LinkInterface::TABLE_NAME, ['entity_id']),
            ['entity_id']
        )->setComment(
            'Amasty Mega Menu Link Table'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable(ItemInterface::TABLE_NAME)
        )->addColumn(
            ItemInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Amasty Menu Item Auto ID'
        )->addColumn(
            ItemInterface::ENTITY_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'nullable' => false, 'primary' => false],
            'Amasty Menu Item ID'
        )->addColumn(
            ItemInterface::TYPE,
            Table::TYPE_TEXT,
            20,
            ['nullable' => false],
            'Amasty Menu Item Type (category or amasty link)'
        )->addColumn(
            ItemInterface::STORE_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'nullable' => false, 'primary' => false],
            'Store ID'
        )->addColumn(
            ItemInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Amasty Menu Item Name'
        )->addColumn(
            ItemInterface::LABEL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Amasty Menu Label'
        )->addColumn(
            ItemInterface::LABEL_TEXT_COLOR,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Amasty Menu Label Color'
        )->addColumn(
            ItemInterface::LABEL_BACKGROUND_COLOR,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Amasty Menu Label Color'
        )->addColumn(
            ItemInterface::STATUS,
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => true, 'default' => 0],
            'Amasty Menu Item Status'
        )->addColumn(
            ItemInterface::CONTENT,
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Amasty Menu Content'
        )->addColumn(
            ItemInterface::WIDTH,
            Table::TYPE_SMALLINT,
            null,
            ['identity' => false, 'nullable' => true, 'primary' => false],
            'Amasty Menu Item Width'
        )->addColumn(
            ItemInterface::WIDTH_VALUE,
            Table::TYPE_SMALLINT,
            null,
            ['identity' => false, 'nullable' => true, 'primary' => false],
            'Amasty Menu Item Width Value'
        )->addColumn(
            ItemInterface::COLUMN_COUNT,
            Table::TYPE_SMALLINT,
            null,
            ['identity' => false, 'nullable' => true, 'primary' => false],
            'Amasty Menu Item Column Count'
        )->addColumn(
            ItemInterface::SORT_ORDER,
            Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'nullable' => true, 'primary' => false],
            'Amasty Menu Item Sort Order'
        )->addIndex(
            $installer->getIdxName(ItemInterface::TABLE_NAME, ['entity_id', 'store_id', 'type']),
            ['entity_id', 'store_id', 'type'],
            ['type' => 'unique']
        )->setComment(
            'Amasty Mega Menu Link Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
