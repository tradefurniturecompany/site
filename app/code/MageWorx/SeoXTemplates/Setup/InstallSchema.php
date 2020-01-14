<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(ProductMetadataInterface $productMetadata)
    {
        $this->productMetadata = $productMetadata;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'mageworx_seoxtemplates_template_product'
         */
        $tableTemplateProduct = $installer->getConnection()->newTable(
            $installer->getTable('mageworx_seoxtemplates_template_product')
        )->addColumn(
            'template_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                ],
            'Template ID'
        ) ->addColumn('type_id', Table::TYPE_SMALLINT, null, [
                'unsigned'  => true,
                'nullable'  => false,
                ], 'Template Type')

            ->addColumn('name', Table::TYPE_TEXT, 255, [
                'nullable'  => false,
                ], 'Template Name')

            ->addColumn('store_id', Table::TYPE_SMALLINT, null, [
                'unsigned'  => true,
                'nullable'  => false,
                ], 'Store ID')

            ->addColumn('code', Table::TYPE_TEXT, '64k', [
                'nullable'  => false,
                ], 'Template Code')

            ->addColumn('assign_type', Table::TYPE_SMALLINT, null, [
                'unsigned'  => true,
                'nullable'  => false,
                ], 'Assign Type')

            ->addColumn('priority', Table::TYPE_SMALLINT, null, [
                'unsigned'  => true,
                'nullable'  => false,
                ], 'Priority')

            ->addColumn('date_modified', Table::TYPE_DATETIME, null, [
                'nullable'  => true,
                ], 'Last Modify Date')

            ->addColumn('date_apply_start', Table::TYPE_DATETIME, null, [
                'nullable'  => true,
                ], 'Last Apply Start Date')

            ->addColumn('date_apply_finish', Table::TYPE_DATETIME, null, [
                'nullable'  => true,
                ], 'Last Apply Finish Date')

            ->addColumn('scope', Table::TYPE_SMALLINT, null, [
                'unsigned'  => true,
                'nullable'  => false,
                'default'  => 1,
                ], 'Scope')

            ->addColumn('is_use_cron', Table::TYPE_SMALLINT, null, [
                'unsigned'  => true,
                'nullable'  => false,
                'default'  => 2,
                ], 'Is Use Cron')
            ->addForeignKey(
                $installer->getFkName('mageworx_seoxtemplates_template_product', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
        ->setComment('Template Product Table (created by MageWorx SeoXTemplates extension)');

        $installer->getConnection()->createTable($tableTemplateProduct);

        /**
         * Create table 'mageworx_seoxtemplates_template_relation_product'
         */
        $tableTemplateProductRelation = $installer->getConnection()->newTable(
            $installer->getTable('mageworx_seoxtemplates_template_relation_product')
        )->addColumn('id', Table::TYPE_INTEGER, null, [
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                ], 'ID')

            ->addColumn('template_id', Table::TYPE_INTEGER, null, [
                'unsigned'  => true,
                'nullable'  => false,
                ], 'Template ID')

            ->addColumn('product_id', Table::TYPE_INTEGER, null, [
                'unsigned'  => true,
                'nullable'  => false,
                ], 'Product ID')

            ->addForeignKey(
                $installer->getFkName('mageworx_seoxtemplates_template_relation_product', 'template_id', 'mageworx_seoxtemplates_template_product', 'template_id'),
                'template_id',
                $installer->getTable('mageworx_seoxtemplates_template_product'),
                'template_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Template Product To Product Link Table (created by MageWorx SeoXTemplates extension)');

        if ($this->productMetadata->getEdition() == 'Enterprise') {
            $tableTemplateProductRelation->addForeignKey(
                $installer->getFkName('mageworx_seoxtemplates_template_relation_product', 'product_id', 'sequence_product', 'sequence_value'),
                'product_id',
                $installer->getTable('sequence_product'),
                'sequence_value',
                Table::ACTION_CASCADE
            );
        } else {
            $tableTemplateProductRelation->addForeignKey(
                $installer->getFkName('mageworx_seoxtemplates_template_relation_product', 'product_id', 'catalog_product_entity', 'entity_id'),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            );
        }

        $installer->getConnection()->createTable($tableTemplateProductRelation);

        /**
         * Create table 'mageworx_seoxtemplates_template_relation_attributeset'
         */
        $tableTemplateProductAttributeRelation = $installer->getConnection()->newTable(
            $installer->getTable('mageworx_seoxtemplates_template_relation_attributeset')
        )->addColumn('id', Table::TYPE_INTEGER, null, [
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                ], 'ID')

            ->addColumn('template_id', Table::TYPE_INTEGER, null, [
                'unsigned'  => true,
                'nullable'  => false,
                ], 'Template ID')

            ->addColumn('attributeset_id', Table::TYPE_SMALLINT, null, [
                'unsigned'  => true,
                'nullable'  => false,
                ], 'Attribute Set ID')

            ->addForeignKey(
                $installer->getFkName('mageworx_seoxtemplates_template_relation_attributeset', 'template_id', 'mageworx_seoxtemplates_template_product', 'template_id'),
                'template_id',
                $installer->getTable('mageworx_seoxtemplates_template_product'),
                'template_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('mageworx_seoxtemplates_template_relation_attributeset', 'attributeset_id', 'eav_attribute_set', 'attribute_set_id'),
                'attributeset_id',
                $installer->getTable('eav_attribute_set'),
                'attribute_set_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Template Product To Product Attribute Set Link Table (created by MageWorx SeoXTemplates extension)');

        $installer->getConnection()->createTable($tableTemplateProductAttributeRelation);

   /**
    * Create table 'mageworx_seoxtemplates_template_product'
    */

        $tableTemplateCategory = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoxtemplates_template_category'))
        ->addColumn('template_id', Table::TYPE_INTEGER, null, [
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ], 'Template ID')

        ->addColumn('type_id', Table::TYPE_SMALLINT, null, [
            'unsigned'  => true,
            'nullable'  => false,
            ], 'Template Type')

        ->addColumn('name', Table::TYPE_TEXT, 255, [
            'nullable'  => false,
            ], 'Template Name')

        ->addColumn('store_id', Table::TYPE_SMALLINT, null, [
            'unsigned'  => true,
            'nullable'  => false,
            ], 'Store ID')

        ->addColumn('code', Table::TYPE_TEXT, '64k', [
            'nullable'  => false,
            ], 'Template Code')

        ->addColumn('assign_type', Table::TYPE_SMALLINT, null, [
            'unsigned'  => true,
            'nullable'  => false,
            ], 'Assign Type')

        ->addColumn('priority', Table::TYPE_SMALLINT, null, [
            'unsigned'  => true,
            'nullable'  => false,
            ], 'Priority')

        ->addColumn('date_modified', Table::TYPE_DATETIME, null, [
            'nullable'  => true,
            ], 'Last Modify Date')

        ->addColumn('date_apply_start', Table::TYPE_DATETIME, null, [
            'nullable'  => true,
            ], 'Last Apply Start Date')

        ->addColumn('date_apply_finish', Table::TYPE_DATETIME, null, [
            'nullable'  => true,
            ], 'Last Apply Finish Date')

        ->addColumn('scope', Table::TYPE_SMALLINT, null, [
            'unsigned'  => true,
            'nullable'  => false,
            'default'  => 1,
            ], 'Scope')

        ->addColumn('is_use_cron', Table::TYPE_SMALLINT, null, [
            'unsigned'  => true,
            'nullable'  => false,
            'default'  => 2,
            ], 'Is Use Cron')

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates_template_category',
                'store_id',
                'store',
                'store_id'
            ),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )
        ->setComment('Template Category Table (created by MageWorx SeoXTemplates extension)');

        $installer->getConnection()->createTable($tableTemplateCategory);

        /**
         * Create table 'mageworx_seoxtemplates_template_relation_category'
         */
        $tableTemplateCategoryRelation  = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoxtemplates_template_relation_category'))
        ->addColumn('id', Table::TYPE_INTEGER, null, [
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ], 'ID')

        ->addColumn('template_id', Table::TYPE_INTEGER, null, [
            'unsigned'  => true,
            'nullable'  => false,
            ], 'Template ID')

        ->addColumn('category_id', Table::TYPE_INTEGER, null, [
            'unsigned'  => true,
            'nullable'  => false,
            ], 'Category ID')

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates_template_relation_category',
                'template_id',
                'mageworx_seoxtemplates_template_category',
                'template_id'
            ),
            'template_id',
            $installer->getTable('mageworx_seoxtemplates_template_category'),
            'template_id',
            Table::ACTION_CASCADE
        )
        ->setComment('Template Category To Category Link Table (created by MageWorx SeoXTemplates extension)');

        if ($this->productMetadata->getEdition() == 'Enterprise') {
            $tableTemplateCategoryRelation->addForeignKey(
                $installer->getFkName(
                    'mageworx_seoxtemplates_template_relation_category',
                    'category_id',
                    'sequence_catalog_category',
                    'sequence_value'
                ),
                'category_id',
                $installer->getTable('sequence_catalog_category'),
                'sequence_value',
                Table::ACTION_CASCADE
            );
        } else {
            $tableTemplateCategoryRelation->addForeignKey(
                $installer->getFkName(
                    'mageworx_seoxtemplates_template_relation_category',
                    'category_id',
                    'catalog_category_entity',
                    'entity_id'
                ),
                'category_id',
                $installer->getTable('catalog_category_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            );
        }

        $installer->getConnection()->createTable($tableTemplateCategoryRelation);
        $installer->endSetup();
    }
}
