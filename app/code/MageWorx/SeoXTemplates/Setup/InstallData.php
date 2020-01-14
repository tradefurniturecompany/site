<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Setup;

use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Catalog\Setup\CategorySetup $catalogSetup */

        $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $catalogSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'product_seo_name',
            [
                'group' => 'Search Engine Optimization',
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'SEO Name',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'apply_to' => '',
                'visible_on_front' => false,
                'note' => 'This setting was added by MageWorx SEO Extended Templates'
            ]
        );

        $catalogSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'category_seo_name',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'SEO Name',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'apply_to' => '',
                'visible_on_front' => false,
                'sort_order' => 6,
                'note' => 'This setting was added by MageWorx SEO Extended Templates'
            ]
        );
    }
}
