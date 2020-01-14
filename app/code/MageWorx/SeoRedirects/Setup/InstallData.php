<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Setup;

use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    const REDIRECT_PRIORITY_CODE = 'redirect_priority';

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
            \Magento\Catalog\Model\Category::ENTITY,
            self::REDIRECT_PRIORITY_CODE,
            [
                'group'            => 'Search Engine Optimization',
                'type'             => 'text',
                'backend'          => '',
                'frontend'         => '',
                'label'            => 'Product Redirect Priority',
                'input'            => 'text',
                'class'            => '',
                'source'           => '',
                'global'           => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'visible'          => true,
                'required'         => false,
                'user_defined'     => false,
                'default'          => 0,
                'apply_to'         => '',
                'visible_on_front' => false,
                'sort_order'       => 9,
                'frontend_class'   => 'validate-percents',
                'note'             => '100 is the highest priority.'
            ]
        );
    }
}
