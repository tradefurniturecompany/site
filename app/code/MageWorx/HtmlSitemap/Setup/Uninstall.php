<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Setup;

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
        $categorySetupManager->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'in_html_sitemap');
        $categorySetupManager->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'in_html_sitemap');

        $connection = $setup->getConnection();
        $connection->dropColumn($connection->getTableName('cms_page'), 'in_html_sitemap');

        $setup->endSetup();
    }
}
