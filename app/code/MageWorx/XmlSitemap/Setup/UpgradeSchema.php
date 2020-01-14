<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\App\ProductMetadataInterface;
use MageWorx\XmlSitemap\Model\Source\EntityType;

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
    public function upgrade(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $installer->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $installer->getConnection()
                ->addColumn($installer->getTable('sitemap'), 'count_by_entity', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Count by entity statistic (added by MageWorx XMLSitemap)',
                ]);

            $installer->getConnection()
                ->addColumn($installer->getTable('cms_page'), 'in_xml_sitemap', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'length' => 1,
                    'default' => '1',
                    'comment' => 'Use in XML sitemap (added by MageWorx XMLSitemap)',
                    'unsigned' => true
                ]);
        }

        if (version_compare($context->getVersion(), '2.0.2', '<')) {

            if ($installer->getConnection()->isTableExists($installer->getTable('sitemap'))) {

                $installer->getConnection()
                    ->addColumn(
                        $installer->getTable('sitemap'),
                        'entity_type',
                        [
                            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'length'   => 32,
                            'default'  => EntityType::DEFAULT_TYPE,
                            'nullable' => false,
                            'comment'  => 'Entity type code (added by MageWorx XMLSitemap)',
                        ]
                    );
            }
        }

        if (version_compare($context->getVersion(), '2.0.3', '<')) {

            if ($installer->getConnection()->isTableExists($installer->getTable('sitemap'))) {

                $installer->getConnection()
                    ->modifyColumn(
                        $installer->getTable('sitemap'),
                        'sitemap_filename',
                        [
                            'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'length'    => 64,
                            'comment'   => 'Sitemap Filename'
                        ]
                    );
            }
        }
        $installer->endSetup();
    }
}
