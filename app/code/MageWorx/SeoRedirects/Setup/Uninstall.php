<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Setup;

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
        $categorySetupManager->removeAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            \MageWorx\SeoRedirects\Setup\InstallData::REDIRECT_PRIORITY_CODE
        );

        $connection = $setup->getConnection();
        $connection->dropTable($connection->getTableName('mageworx_seoredirects_redirect_dp'));
        $connection->dropTable($connection->getTableName('mageworx_seoredirects_redirect_custom'));

        $setup->endSetup();
    }
}
