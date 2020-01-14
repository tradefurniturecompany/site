<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class Uninstall implements UninstallInterface
{
    /**
     * Module uninstall code
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $connection = $setup->getConnection();
        $connection->dropTable($connection->getTableName('mageworx_seoreports_product'));
        $connection->dropTable($connection->getTableName('mageworx_seoreports_category'));
        $connection->dropTable($connection->getTableName('mageworx_seoreports_page'));

        $setup->endSetup();
    }
}
