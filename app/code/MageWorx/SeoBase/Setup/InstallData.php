<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Setup;

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
     * @var Default value fot "meta_robots" attribute
     */
    const META_ROBOTS_DEFAULT_VALUE = '';

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
        $attributeCode = 'meta_robots';
        $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $catalogSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeCode,
            [
                'group' => 'Search Engine Optimization',
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Meta Robots',
                'input' => 'select',
                'class' => '',
                'source' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => self::META_ROBOTS_DEFAULT_VALUE,
                'apply_to' => '',
                'visible_on_front' => false,
                'note' => 'This setting was added by MageWorx SEO Suite'
            ]
        );

        $catalogSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            $attributeCode,
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Meta Robots',
                'input' => 'select',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => self::META_ROBOTS_DEFAULT_VALUE,
                'apply_to' => '',
                'visible_on_front' => false,
                'sort_order' => 9,
                'note' => 'This setting was added by MageWorx SEO Suite'
            ]
        );

        $catalogSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'cross_domain_store',
            [
                'group' => 'Search Engine Optimization',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Cross Domain Store',
                'input' => 'select',
                'class' => '',
                'source' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'apply_to' => '',
                'visible_on_front' => false,
                'note' => 'This setting was added by MageWorx SEO Suite'
            ]
        );

        $catalogSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'cross_domain_url',
            [
                'group' => 'Search Engine Optimization',
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Cross Domain URL',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'apply_to' => '',
                'frontend_class' => 'validate-url',
                'visible_on_front' => false,
                'note' => 'This setting was added by MageWorx SEO Suite'
            ]
        );
    }
}
