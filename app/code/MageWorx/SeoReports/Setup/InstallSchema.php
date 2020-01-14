<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use MageWorx\SeoAll\Model\SetupInitiator;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var SetupInitiator
     */
    protected $setupInitiator;

    /**
     * @param SetupInitiator $setupInitiator
     */
    public function __construct(
        SetupInitiator $setupInitiator
    ) {
        $this->setupInitiator = $setupInitiator;
    }


    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->createProductReportTable($installer);
        $this->createCategoryReportTable($installer);
        $this->createCmsReportTable($installer);

        $this->setupInitiator->call('MageWorx_SeoReports', $setup);
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    protected function createProductReportTable($installer)
    {
        /**
         * Create table 'mageworx_seoreports_product'
         */
        $table = $installer
            ->getConnection()
            ->newTable(
                $installer->getTable('mageworx_seoreports_product')
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                11,
                array(
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true,
                ),
                'Entity ID'
            )
            ->addColumn(
                'reference_id',
                Table::TYPE_INTEGER,
                null,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                ),
                'Product ID'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                5,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                    'default'  => '0',
                ),
                'Store ID'
            )
            ->addColumn(
                'sku',
                Table::TYPE_TEXT,
                64,
                array(
                    'nullable' => false,
                ),
                'Product SKU'
            )
            ->addColumn(
                'url_path',
                Table::TYPE_TEXT,
                1024,
                array(
                    'nullable' => false,
                ),
                'Product URL Path'
            )
            ->addColumn(
                'url_path_length',
                Table::TYPE_SMALLINT,
                3,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                ),
                'Product URL Path Length'
            )
            ->addColumn(
                'type_id',
                Table::TYPE_TEXT,
                32,
                array(
                    'nullable' => false,
                ),
                'Product Type'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                1024,
                array(
                    'nullable' => false,
                ),
                'Product Name'
            )
            ->addColumn(
                'prepared_name',
                Table::TYPE_TEXT,
                1024,
                array(
                    'nullable' => false,
                ),
                'Product Prepared Name'
            )
            ->addColumn(
                'name_duplicate_count',
                Table::TYPE_SMALLINT,
                5,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                    'default'  => 0,
                ),
                'Product Name Duplicate Count'
            )
            ->addColumn(
                'name_length',
                Table::TYPE_SMALLINT,
                3,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                ),
                'Product Name Length'
            )
            ->addColumn(
                'meta_title',
                Table::TYPE_TEXT,
                1024,
                array(
                    'nullable' => false,
                ),
                'Product Meta Title'
            )
            ->addColumn(
                'prepared_meta_title',
                Table::TYPE_TEXT,
                1024,
                array(
                    'nullable' => false,
                ),
                'Product Prepared Meta Title'
            )
            ->addColumn(
                'meta_title_length',
                Table::TYPE_SMALLINT,
                3,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                ),
                'Product Meta Title Length'
            )
            ->addColumn(
                'meta_title_duplicate_count',
                Table::TYPE_SMALLINT,
                5,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                    'default'  => 0,
                ),
                'Product Meta Title Duplicate Count'
            )
            ->addColumn(
                'meta_description_length',
                Table::TYPE_SMALLINT,
                5,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                ),
                'Product Meta Description Length'
            )
            ->addIndex(
                $installer->getIdxName('mageworx_seoreports_product', 'prepared_name'),
                array(
                    'prepared_name' => array('name' => 'prepared_name', 'size' => 8)
                )
            )
            ->addIndex(
                $installer->getIdxName('mageworx_seoreports_product', 'prepared_meta_title'),
                array(
                    'prepared_meta_title' => array('name' => 'prepared_meta_title', 'size' => 8)
                )
            )
            ->addIndex(
                $installer->getIdxName(
                    'mageworx_seoreports_product',
                    array('entity_id', 'reference_id', 'store_id')
                ),
                array('entity_id', 'reference_id', 'store_id')
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mageworx_seoreports_product',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE,
                Table::ACTION_CASCADE
            );

        $installer->getConnection()->createTable($table);
    }


    /**
     * @param SchemaSetupInterface $installer
     */
    protected function createCategoryReportTable($installer)
    {
        /**
         * Create table 'mageworx_seoreports_category'
         */
        $table = $installer
            ->getConnection()
            ->newTable($installer->getTable('mageworx_seoreports_category'))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                11,
                array(
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true,
                ),
                'Entity ID'
            )
            ->addColumn(
                'reference_id',
                Table::TYPE_INTEGER,
                10,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                ),
                'Category ID'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                5,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                    'default'  => '0',
                ),
                'Store ID'
            )
            ->addColumn(
                'level',
                Table::TYPE_SMALLINT,
                10,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                ),
                'Category Level'
            )
            ->addColumn(
                'path',
                Table::TYPE_TEXT,
                255,
                array(
                    'nullable' => false,
                ),
                'Category Path'
            )
            ->addColumn(
                'url_path',
                Table::TYPE_TEXT,
                1024,
                array(
                    'nullable' => false,
                ),
                'Category URL Path'
            )
            ->addColumn(
                'url_path_length',
                Table::TYPE_SMALLINT,
                3,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                ),
                'Category URL Path Length'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                1024,
                array(
                    'nullable' => false,
                ),
                'Category Name'
            )
            ->addColumn(
                'prepared_name',
                Table::TYPE_TEXT,
                1024,
                array(
                    'nullable' => false,
                ),
                'Category Prepared Name'
            )
            ->addColumn(
                'name_duplicate_count',
                Table::TYPE_SMALLINT,
                5,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                    'default'  => 0,
                ),
                'Category Name Duplicate Count'
            )
            ->addColumn(
                'name_length',
                Table::TYPE_SMALLINT,
                3,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                ),
                'Category Name Length'
            )
            ->addColumn(
                'meta_title',
                Table::TYPE_TEXT,
                1024,
                array(
                    'nullable' => false,
                ),
                'Category Meta Title'
            )
            ->addColumn(
                'prepared_meta_title',
                Table::TYPE_TEXT,
                1024,
                array(
                    'nullable' => false,
                ),
                'Category Prepared Meta Title'
            )
            ->addColumn(
                'meta_title_length',
                Table::TYPE_SMALLINT,
                3,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                ),
                'Category Meta Title Length'
            )
            ->addColumn(
                'meta_title_duplicate_count',
                Table::TYPE_SMALLINT,
                5,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                    'default'  => 0,
                ),
                'Category Meta Title Duplicate Count'
            )
            ->addColumn(
                'meta_description_length',
                Table::TYPE_SMALLINT,
                5,
                array(
                    'unsigned' => true,
                    'nullable' => false,
                ),
                'Category Meta Description Length'
            )
            ->addIndex(
                $installer->getIdxName('mageworx_seoreports_category', 'prepared_name'),
                array(
                    'prepared_name' => array('name' => 'prepared_name', 'size' => 8)
                )
            )
            ->addIndex(
                $installer->getIdxName('mageworx_seoreports_category', 'prepared_meta_title'),
                array(
                    'prepared_meta_title' => array('name' => 'prepared_meta_title', 'size' => 8)
                )
            )
            ->addIndex(
                $installer->getIdxName('mageworx_seoreports_category', array('entity_id', 'reference_id', 'store_id')),
                array('entity_id', 'reference_id', 'store_id')
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mageworx_seoreports_category',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE,
                Table::ACTION_CASCADE
            );

        $installer->getConnection()->createTable($table);
    }


    /**
     * @param SchemaSetupInterface $installer
     */
    protected function createCmsReportTable($installer)
    {
        $table =
            $installer
                ->getConnection()
                ->newTable($installer->getTable('mageworx_seoreports_page'))
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    11,
                    array(
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary'  => true,
                    ),
                    'Entity ID'
                )
                ->addColumn(
                    'reference_id',
                    Table::TYPE_SMALLINT,
                    6,
                    array(
                        'nullable' => false,
                    ),
                    'Page ID'
                )
                ->addColumn(
                    'store_id',
                    Table::TYPE_SMALLINT,
                    5,
                    array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default'  => '0',
                    ),
                    'Store ID'
                )
                ->addColumn(
                    'url_path',
                    Table::TYPE_TEXT,
                    1024,
                    array(
                        'nullable' => false,
                    ),
                    'Page URL Path'
                )
                ->addColumn(
                    'url_path_length',
                    Table::TYPE_SMALLINT,
                    5,
                    array(
                        'unsigned' => true,
                        'nullable' => false,
                    ),
                    'Page URL Key Length'
                )
                ->addColumn(
                    'heading',
                    Table::TYPE_TEXT,
                    1024,
                    array(
                        'nullable' => false,
                    ),
                    'Page Heading'
                )
                ->addColumn(
                    'prepared_heading',
                    Table::TYPE_TEXT,
                    1024,
                    array(
                        'nullable' => false,
                    ),
                    'Page Prepared Heading'
                )
                ->addColumn(
                    'heading_length',
                    Table::TYPE_SMALLINT,
                    3,
                    array(
                        'unsigned' => true,
                        'nullable' => false,
                    ),
                    'Page Heading Length'
                )
                ->addColumn(
                    'heading_duplicate_count',
                    Table::TYPE_SMALLINT,
                    5,
                    array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default'  => 0,
                    ),
                    'Page Heading Duplicate Count'
                )
                ->addColumn(
                    'title',
                    Table::TYPE_TEXT,
                    1024,
                    array(
                        'nullable' => false,
                    ),
                    'Page Title'
                )
                ->addColumn(
                    'prepared_title',
                    Table::TYPE_TEXT,
                    1024,
                    array(
                        'nullable' => false,
                    ),
                    'Page Prepared Title'
                )
                ->addColumn(
                    'title_length',
                    Table::TYPE_SMALLINT,
                    3,
                    array(
                        'unsigned' => true,
                        'nullable' => false,
                    ),
                    'Page Title Length'
                )
                ->addColumn(
                    'title_duplicate_count',
                    Table::TYPE_SMALLINT,
                    5,
                    array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default'  => 0,
                    ),
                    'Page Title Duplicate Count'
                )
                ->addColumn(
                    'meta_title',
                    Table::TYPE_TEXT,
                    1024,
                    array(
                        'nullable' => false,
                    ),
                    'Page Meta Title'
                )
                ->addColumn(
                    'prepared_meta_title',
                    Table::TYPE_TEXT,
                    1024,
                    array(
                        'nullable' => false,
                    ),
                    'Page Prepared Meta Title'
                )
                ->addColumn(
                    'meta_title_length',
                    Table::TYPE_SMALLINT,
                    3,
                    array(
                        'unsigned' => true,
                        'nullable' => false,
                    ),
                    'Page Meta Title Length'
                )
                ->addColumn(
                    'meta_title_duplicate_count',
                    Table::TYPE_SMALLINT,
                    5,
                    array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default'  => 0,
                    ),
                    'Page Meta Title Duplicate Count'
                )
                ->addColumn(
                    'meta_description_length',
                    Table::TYPE_SMALLINT,
                    5,
                    array(
                        'unsigned' => true,
                        'nullable' => false,
                    ),
                    'Page Meta Description Length'
                )
                ->addIndex(
                    $installer->getIdxName('mageworx_seoreports_page', 'prepared_heading'),
                    array(
                        'prepared_heading' => array('name' => 'prepared_heading', 'size' => 8)
                    )
                )
                ->addIndex(
                    $installer->getIdxName('mageworx_seoreports_page', 'prepared_meta_title'),
                    array(
                        'prepared_meta_title' => array('name' => 'prepared_meta_title', 'size' => 8)
                    )
                )
                ->addIndex(
                    $installer->getIdxName('mageworx_seoreports_page', array('entity_id', 'reference_id', 'store_id')),
                    array('entity_id', 'reference_id', 'store_id')
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'mageworx_seoreports_page',
                        'store_id',
                        'store',
                        'store_id'
                    ),
                    'store_id',
                    $installer->getTable('store'),
                    'store_id',
                    Table::ACTION_CASCADE,
                    Table::ACTION_CASCADE
                );


        $installer->getConnection()->createTable($table);
    }
}
