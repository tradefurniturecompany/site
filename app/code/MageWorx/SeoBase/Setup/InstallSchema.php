<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Setup;

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

        $installer->getConnection()
            ->addColumn(
                $installer->getTable('cms_page'),
                'meta_robots',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => false,
                    'length'    => 255,
                    'comment'   => 'Meta Robots (added by MageWorx SeoBase)',
                    'after'     => 'meta_description'
                ]
            );

        $installer->getConnection()
            ->addColumn(
                $installer->getTable('catalog_eav_attribute'),
                'layered_navigation_canonical',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable'  => false,
                    'unsigned'  => true,
                    'length'    => 1,
                    'default'   => '0',
                    'comment'   => 'Use In Canonical (added by MageWorx SeoBase)',
                ]
            );
        $installer->endSetup();
    }
}
