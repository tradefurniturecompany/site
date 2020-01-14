<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Setup;

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
            ->addColumn($installer->getTable('cms_page'), 'in_html_sitemap', [
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable'  => false,
                'length'    => 1,
                'default'   => '1',
                'comment'   => 'Use in HTML Sitemap (added by MageWorx HtmlSitemap)',
                'unsigned'  => true,
                'after'    => 'update_time'
            ]);

        $installer->endSetup();
    }
}
