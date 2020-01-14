<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Setup;

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
         * Create table 'mageworx_seoredirects_redirect_dp'
         */
        $tableDeadProductRedirect = $installer->getConnection()->newTable(
            $installer->getTable('mageworx_seoredirects_redirect_dp')
        )
                                              ->addColumn(
                                                  'redirect_id',
                                                  \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                                                  null,
                                                  [
                                                      'identity' => true,
                                                      'unsigned' => true,
                                                      'nullable' => false,
                                                      'primary'  => true,
                                                  ],
                                                  'Redirect ID'
                                              )
                                              ->addColumn(
                                                  'product_id',
                                                  \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                                                  null,
                                                  [
                                                      'unsigned' => true,
                                                      'nullable' => true,
                                                  ],
                                                  'Product ID'
                                              )
                                              ->addColumn(
                                                  'product_name',
                                                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                                  1024,
                                                  [],
                                                  'Deleted Product Name'
                                              )
                                              ->addColumn(
                                                  'product_sku',
                                                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                                  1024,
                                                  [],
                                                  'Deleted Product SKU'
                                              )
                                              ->addColumn(
                                                  'store_id',
                                                  \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                                                  null,
                                                  [
                                                      'unsigned' => true,
                                                      'nullable' => false,
                                                      'default'  => '0'
                                                  ],
                                                  'Store Id'
                                              )
                                              ->addColumn(
                                                  'request_path',
                                                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                                  1024,
                                                  [],
                                                  'Request Path'
                                              )
                                              ->addColumn(
                                                  'category_id',
                                                  \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                                                  null,
                                                  [
                                                      'unsigned' => true,
                                                      'nullable' => true,
                                                  ],
                                                  'Request Category ID'
                                              )
                                              ->addColumn(
                                                  'priority_category_id',
                                                  \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                                                  null,
                                                  [
                                                      'unsigned' => true,
                                                      'nullable' => true,
                                                  ],
                                                  'Targeted Category ID'
                                              )
                                              ->addColumn(
                                                  'date_created',
                                                  \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                                                  null,
                                                  [
                                                      'nullable' => false,
                                                      'default'  => '0000-00-00 00:00:00',
                                                  ],
                                                  'Date Created'
                                              )
                                              ->addColumn(
                                                  'hits',
                                                  \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                                                  null,
                                                  [
                                                      'unsigned' => true,
                                                      'nullable' => false,
                                                      'default'  => 0,
                                                  ],
                                                  'Hits'
                                              )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default'  => 1,
                ],
                'Is Active'
            );

        $installer->getConnection()->createTable($tableDeadProductRedirect);
        $installer->endSetup();
    }
}
