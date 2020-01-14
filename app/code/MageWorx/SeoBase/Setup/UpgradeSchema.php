<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;
use MageWorx\SeoBase\Api\Data\CustomCanonicalInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->addHreflangIdentifierToCmsPage($setup);
        }

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $this->addDefaultValueForMetaRobots($setup);
        }

        if (version_compare($context->getVersion(), '2.0.5', '<')) {
            $this->updateCategoryLnMetaRobotsSetting($setup);
        }

        if (version_compare($context->getVersion(), '2.0.6', '<')) {
            $this->addCustomCanonicalTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function updateCategoryLnMetaRobotsSetting(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->update(
            $setup->getTable('core_config_data'),
            ['path' => 'mageworx_seo/base/robots/category_ln_pages_robots'],
            "path = 'mageworx_seo/base/robots/category_filter_to_noindex'"
        );

        $setup->getConnection()->update(
            $setup->getTable('core_config_data'),
            ['value' => 'NOINDEX, FOLLOW'],
            "path = 'mageworx_seo/base/robots/category_ln_pages_robots' AND value = '1'"
        );

        $setup->getConnection()->update(
            $setup->getTable('core_config_data'),
            ['value' => ''],
            "path = 'mageworx_seo/base/robots/category_ln_pages_robots' AND value = '0'"
        );
    }

    /**
     * Add Hreflang Identifier column
     * @param SchemaSetupInterface $setup
     */
    private function addHreflangIdentifierToCmsPage(SchemaSetupInterface $setup)
    {
         $setup->getConnection()->addColumn(
             $setup->getTable('cms_page'),
             'mageworx_hreflang_identifier',
             [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Added by MageWorx for Hreflang URLs',
                    'after'     => 'identifier'
                ]
         );
    }

    /**
     * Add default value for the meta robots column
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function addDefaultValueForMetaRobots(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $connection->modifyColumn(
            $setup->getTable('cms_page'),
            'meta_robots',
            [
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable'  => false,
                'length'    => 255,
                'comment'   => 'Meta Robots (added by MageWorx SeoBase)',
                'default'   => '',
                'after'     => 'meta_description'
            ]
        );
    }

    private function addCustomCanonicalTable(SchemaSetupInterface $setup)
    {
        $tableName = 'mageworx_seobase_custom_canonical';

        $сustomCanonicalTable = $setup->getConnection()
            ->newTable($setup->getTable($tableName))
            ->addColumn(
                CustomCanonicalInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Entity Id'
            )->addColumn(
                CustomCanonicalInterface::SOURCE_ENTITY_TYPE,
                Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'Source Entity Type Code'
            )->addColumn(
                CustomCanonicalInterface::SOURCE_ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Source Entity Id'
            )->addColumn(
                CustomCanonicalInterface::SOURCE_STORE_ID,
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Source Store Id'
            )->addColumn(
                CustomCanonicalInterface::TARGET_ENTITY_TYPE,
                Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'Target Entity Type Code'
            )->addColumn(
                CustomCanonicalInterface::TARGET_ENTITY_ID,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Target Entity Id'
            )->addColumn(
                CustomCanonicalInterface::TARGET_STORE_ID,
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Target Store Id'
            )->addIndex(
                $setup->getIdxName(
                    $tableName,
                    [
                        CustomCanonicalInterface::SOURCE_ENTITY_TYPE,
                        CustomCanonicalInterface::SOURCE_ENTITY_ID,
                        CustomCanonicalInterface::SOURCE_STORE_ID
                    ],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [
                    CustomCanonicalInterface::SOURCE_ENTITY_TYPE,
                    CustomCanonicalInterface::SOURCE_ENTITY_ID,
                    CustomCanonicalInterface::SOURCE_STORE_ID
                ],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addForeignKey(
                $setup->getFkName(
                    $tableName,
                    CustomCanonicalInterface::SOURCE_STORE_ID,
                    'store',
                    'store_id'
                ),
                CustomCanonicalInterface::SOURCE_STORE_ID,
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    $tableName,
                    CustomCanonicalInterface::TARGET_STORE_ID,
                    'store',
                    'store_id'
                ),
                CustomCanonicalInterface::TARGET_STORE_ID,
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            );

        $setup->getConnection()->createTable($сustomCanonicalTable);
    }
}
