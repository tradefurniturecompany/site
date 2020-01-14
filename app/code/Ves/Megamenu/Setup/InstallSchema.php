<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'Ves Megamenu Menu'
         */
        $setup->getConnection()->dropTable($setup->getTable('ves_megamenu_menu'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_megamenu_menu')
        )
        ->addColumn(
            'menu_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Menu ID'
        )
        ->addColumn(
            'alias',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Alias'
            )
        ->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Menu Name'
            )
        ->addColumn(
            'mobile_template',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Mobile Template'
            )
        ->addColumn(
            'structure',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Structure'
            )
        ->addColumn(
            'disable_bellow',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Disable Bellow'
            )
        ->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Status'
            )
        ->addColumn(
            'html',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Html'
            )
        ->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Menu Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Menu Modification Time'
        )
        ->addIndex(
                $setup->getIdxName('ves_megamenu_menu', ['menu_id']),
                ['menu_id']
                );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'Ves Megamenu Menu'
         */
        $setup->getConnection()->dropTable($setup->getTable('ves_megamenu_item'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_megamenu_item')
        )
        ->addColumn(
            'id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Item ID'
        )
        ->addColumn(
            'item_id',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'ID'
        )
        ->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Item Name'
            )
        ->addColumn(
            'show_name',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => true],
            'Show Name'
            )
        ->addColumn(
            'classes',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Classes'
            )
        ->addColumn(
            'child_col',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Child Menu Col'
            )
        ->addColumn(
            'sub_width',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Sub Width'
            )
        ->addColumn(
            'align',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Alignment Type'
            )
        ->addColumn(
            'icon_position',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Icon Position'
            )
        ->addColumn(
            'icon_classes',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Icon Classes'
            )
        ->addColumn(
            'is_group',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => true],
            'Is Group'
            )
        ->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => true],
            'Status'
            )
        ->addColumn(
            'disable_bellow',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Disable Bellow'
            )
        ->addColumn(
            'show_icon',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Show Icon'
            )
        ->addColumn(
            'icon',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Icon'
            )
        ->addColumn(
            'show_header',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => true],
            'Show Header'
            )
        ->addColumn(
            'header_html',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Header'
            )
        ->addColumn(
            'show_left_sidebar',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => true],
            'Show Left Sidebar'
            )
        ->addColumn(
            'left_sidebar_width',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Left Sidebar Width'
            )
        ->addColumn(
            'menu_id',
            Table::TYPE_SMALLINT,
            null,
            [],
            'Menu ID'
        )
        ->addColumn(
            'left_sidebar_html',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Left Sidebar HTML'
            )
        ->addColumn(
            'show_content',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => true],
            'Show Content'
            )
        ->addColumn(
            'content_width',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Content Width'
            )
        ->addColumn(
            'content_type',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Content Type'
            )
        ->addColumn(
            'link_type',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Link'
            )
        ->addColumn(
            'link',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Link'
            )
        ->addColumn(
            'category',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Link'
            )
        ->addColumn(
            'target',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Link'
            )
        ->addColumn(
            'content_html',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Content HTML'
            )
        ->addColumn(
            'show_right_sidebar',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => true],
            'Show Right Sidebar'
            )
        ->addColumn(
            'right_sidebar_width',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Right Sidebar Width'
            )
        ->addColumn(
            'right_sidebar_html',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Right Sidebar HTML'
            )
        ->addColumn(
            'show_footer',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => true],
            'Show Footer'
            )
        ->addColumn(
            'footer_html',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Footer HTML'
            )
        ->addColumn(
            'color',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Color'
            )
        ->addColumn(
            'hover_color',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Hover Color'
            )
        ->addColumn(
            'bg_color',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Background Color'
            )
        ->addColumn(
            'bg_hover_color',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Background Hover Color'
            )
        ->addColumn(
            'inline_css',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Inline CSS'
            )
        ->addIndex(
                $setup->getIdxName('ves_megamenu_item', ['menu_id']),
                ['menu_id']
                )
        ->addForeignKey(
            $installer->getFkName('ves_megamenu_item_fk', 'menu_id', 'ves_megamenu_menu', 'menu_id'),
            'menu_id',
            $installer->getTable('ves_megamenu_menu'),
            'menu_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Megamenu Menu Item'
        );
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'ves_megamenu_menu_store'
         */
        $setup->getConnection()->dropTable($setup->getTable('ves_megamenu_menu_store'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_megamenu_menu_store')
        )->addColumn(
            'menu_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Menu ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('ves_megamenu_menu_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('ves_megamenu_menu_store', 'menu_id', 'ves_megamenu_menu', 'menu_id'),
            'menu_id',
            $installer->getTable('ves_megamenu_menu'),
            'menu_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('ves_megamenu_menu_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Megamenu Menu Store'
        );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}