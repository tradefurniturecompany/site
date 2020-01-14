<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
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
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<') && $this->productMetadata->getEdition() == 'Enterprise') {
            $this->modifyForeignKeyForEnterpriseEdition($installer);
        }

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $this->installCategoryFilterTables($installer);
        }

        if (version_compare($context->getVersion(), '2.0.4', '<')) {
            $this->addColumnsIsSingleStoreMode($installer);
        }

        if (version_compare($context->getVersion(), '2.0.5', '<')) {
            $this->installLandingPagesTables($installer);
        }

        if (version_compare($context->getVersion(), '2.0.6', '<')) {
            $this->addAttributeOptionColumnForCategoryFilter($installer);
        }

        if (version_compare($context->getVersion(), '2.0.7', '<')) {
            $this->addSeoNameToReportTables($installer);
        }

        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     * @param string $key
     */
    public function setupForRelations($key, SchemaSetupInterface $installer)
    {
        if ($key == 'MageWorx_SeoReports') {
            $this->addSeoNameToReportTables($installer);
        }
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function modifyForeignKeyForEnterpriseEdition($installer)
    {
        $installer->getConnection()->dropForeignKey(
            $installer->getTable('mageworx_seoxtemplates_template_relation_product'),
            $installer->getFkName(
                'mageworx_seoxtemplates_template_relation_product',
                'product_id',
                'catalog_product_entity',
                'entity_id'
            )
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates_template_relation_product',
                'product_id',
                'sequence_product',
                'sequence_value'
            ),
            $installer->getTable('mageworx_seoxtemplates_template_relation_product'),
            'product_id',
            $installer->getTable('sequence_product'),
            'sequence_value',
            Table::ACTION_CASCADE
        );

        $installer->getConnection()->dropForeignKey(
            $installer->getTable('mageworx_seoxtemplates_template_relation_category'),
            $installer->getFkName(
                'mageworx_seoxtemplates_template_relation_category',
                'category_id',
                'catalog_category_entity',
                'entity_id'
            )
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates_template_relation_category',
                'category_id',
                'sequence_catalog_category',
                'sequence_value'
            ),
            $installer->getTable('mageworx_seoxtemplates_template_relation_category'),
            'category_id',
            $installer->getTable('sequence_catalog_category'),
            'sequence_value',
            Table::ACTION_CASCADE
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function installCategoryFilterTables($installer)
    {
        /**
         * Create table 'mageworx_seoxtemplates_template_category_filter'
         */
        $tableTemplateCategory = $installer->getConnection()
            ->newTable($installer->getTable('mageworx_seoxtemplates_template_categoryfilter'))
            ->addColumn('template_id', Table::TYPE_INTEGER, null, [
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ], 'Template ID')

            ->addColumn('attribute_id', Table::TYPE_SMALLINT, null, [
                'unsigned'  => true,
                'nullable'  => false,
            ], 'Category Attribute ID')

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
                    'mageworx_seoxtemplates_template_categoryfilter',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mageworx_seoxtemplates_template_categoryfilter',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $installer->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Template Category Filter Table (created by MageWorx SeoXTemplates extension)');

        $installer->getConnection()->createTable($tableTemplateCategory);

        /**
         * Create table 'mageworx_seoxtemplates_template_relation_categoryfilter'
         */
        $tableTemplateCategoryRelation  = $installer->getConnection()
            ->newTable($installer->getTable('mageworx_seoxtemplates_template_relation_categoryfilter'))
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
                    'mageworx_seoxtemplates_template_relation_categoryfilter',
                    'template_id',
                    'mageworx_seoxtemplates_template_categoryfilter',
                    'template_id'
                ),
                'template_id',
                $installer->getTable('mageworx_seoxtemplates_template_categoryfilter'),
                'template_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Template Category To Category Link Table (created by MageWorx SeoXTemplates extension)');

            $tableTemplateCategoryRelation->addForeignKey(
                $installer->getFkName(
                    'mageworx_seoxtemplates_template_relation_categoryfilter',
                    'category_id',
                    'catalog_category_entity',
                    'entity_id'
                ),
                'category_id',
                $installer->getTable('catalog_category_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            );

        $installer->getConnection()->createTable($tableTemplateCategoryRelation);
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function addColumnsIsSingleStoreMode($installer)
    {
        $templateProductTable  = $installer->getTable('mageworx_seoxtemplates_template_product');
        $templateCategoryTable = $installer->getTable('mageworx_seoxtemplates_template_category');

        $columnName = 'is_single_store_mode';
        $definition = [
            'type' => Table::TYPE_BOOLEAN,
            'nullable' => false,
            'comment' => 'Is Single-Store Mode',
        ];

        $connection = $installer->getConnection();

        if ($installer->getConnection()->isTableExists($templateProductTable)) {

            $connection->addColumn($templateProductTable, $columnName, $definition);
        }

        if ($installer->getConnection()->isTableExists($templateCategoryTable)) {

            $connection->addColumn($templateCategoryTable, $columnName, $definition);
        }
    }

    /**
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function addAttributeOptionColumnForCategoryFilter(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('mageworx_seoxtemplates_template_categoryfilter'),
            'attribute_option_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => 10,
                'unsigned' => true,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Attribute Option ID',
                'after'   => 'attribute_id'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function installLandingPagesTables($installer)
    {
        /**
         * Create table 'mageworx_seoxtemplates_template_landingpage'
         */
        $tableTemplateLandingPage = $installer->getConnection()->newTable(
            $installer->getTable('mageworx_seoxtemplates_template_landingpage')
        )->addColumn(
            'template_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ],
            'Template ID'
        )->addColumn(
            'attribute_id',
            Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Landing Page Attribute ID'
        )->addColumn(
            'type_id',
            Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Template Type'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            [
                'nullable' => false,
            ],
            'Template Name'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Store ID'
        )->addColumn(
            'use_for_default_value',
            Table::TYPE_BOOLEAN,
            null,
            [
                'default'  => false
            ],
            'Use for default value'
        )->addColumn(
            'code',
            Table::TYPE_TEXT,
            '64k',
            [
                'nullable' => false,
            ],
            'Template Code'
        )->addColumn(
            'assign_type',
            Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Assign Type'
        )->addColumn(
            'priority',
            Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Priority'
        )->addColumn(
            'date_modified',
            Table::TYPE_DATETIME,
            null,
            [
                'nullable' => true,
            ],
            'Last Modify Date'
        )->addColumn(
            'date_apply_start',
            Table::TYPE_DATETIME,
            null,
            [
                'nullable' => true,
            ],
            'Last Apply Start Date'
        )->addColumn(
            'date_apply_finish',
            Table::TYPE_DATETIME,
            null,
            [
                'nullable' => true,
            ],
            'Last Apply Finish Date'
        )->addColumn(
            'scope',
            Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default'  => 1,
            ],
            'Scope'
        )->addColumn(
            'is_use_cron',
            Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default'  => 2,
            ],
            'Is Use Cron'
        )->addColumn(
            'is_single_store_mode',
            Table::TYPE_BOOLEAN,
            null,
            [
                'nullable' => false,
                'default'  => 2,
            ],
            'Is Single-Store Mode'
        )->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates_template_landingpage',
                'store_id',
                'store',
                'store_id'
            ),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment('Template Landing Page Table (created by MageWorx SeoXTemplates extension)');

        $installer->getConnection()->createTable($tableTemplateLandingPage);

        /**
         * Create table 'mageworx_seoxtemplates_template_relation_landingpage'
         */
        $tableTemplateLandingPageRelation = $installer->getConnection()->newTable(
            $installer->getTable(
                'mageworx_seoxtemplates_template_relation_landingpage'
            )
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ],
            'ID'
        )->addColumn(
            'template_id',
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Template ID'
        )->addColumn(
            'landingpage_id',
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Landing Page ID'
        )->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates_template_relation_landingpage',
                'template_id',
                'mageworx_seoxtemplates_template_landingpage',
                'template_id'
            ),
            'template_id',
            $installer->getTable(
                'mageworx_seoxtemplates_template_landingpage'
            ),
            'template_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Template Landing Page To Landing Page Link Table (created by MageWorx SeoXTemplates extension)'
        );

        $installer->getConnection()->createTable($tableTemplateLandingPageRelation);
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function addSeoNameToReportTables($installer)
    {
        foreach (['mageworx_seoreports_category', 'mageworx_seoreports_product'] as $table) {

            $connection = $installer->getConnection();
            $reportTable  = $installer->getTable($table);

            if ($installer->getConnection()->isTableExists($reportTable)) {

                $columnName = 'seo_name';
                $definition = [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'SEO Name',
                ];
                $connection->addColumn($reportTable, $columnName, $definition);

                $columnName = 'prepared_seo_name';
                $definition = [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Prepared SEO Name',
                ];
                $connection->addColumn($reportTable, $columnName, $definition);

                $columnName = 'seo_name_length';
                $definition = [
                    'type' => Table::TYPE_SMALLINT,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'SEO Name Length',
                ];
                $connection->addColumn($reportTable, $columnName, $definition);

                $columnName = 'seo_name_duplicate_count';
                $definition = [
                    'type' => Table::TYPE_SMALLINT,
                    'unsigned' => true,
                    'nullable' => false,
                    'default'  => 0,
                    'comment' => 'SEO Name Duplicate Count',
                ];
                $connection->addColumn($reportTable, $columnName, $definition);
            }
        }
    }
}
