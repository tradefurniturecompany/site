<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

class Uninstall implements UninstallInterface
{

    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }

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

        $categorySetupManager = $this->categorySetupFactory->create();
        $categorySetupManager->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'meta_robots');
        $categorySetupManager->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'meta_robots');
        $categorySetupManager->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'cross_domain_store');
        $categorySetupManager->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'cross_domain_url');

        $connection = $setup->getConnection();
        $connection->dropColumn($connection->getTableName('cms_page'), 'meta_robots');
        $connection->dropColumn($connection->getTableName('cms_page'), 'mageworx_hreflang_identifier');
        $connection->dropColumn($connection->getTableName('catalog_eav_attribute'), 'layered_navigation_canonical');

        $connection->dropTable($connection->getTableName('mageworx_seobase_custom_canonical'));

        $setup->endSetup();
    }
}
