<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
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
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.4', '<')) {

            $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $catalogSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'meta_robots',
                [
                    'group' => 'Search Engine Optimization',
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Meta Robots',
                    'input' => 'select',
                    'class' => '',
                    'source' => 'MageWorx\SeoBase\Model\Source\MetaRobots',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => InstallData::META_ROBOTS_DEFAULT_VALUE,
                    'apply_to' => '',
                    'visible_on_front' => false,
                    'note' => 'This setting was added by MageWorx SEO Suite'
                ]
            );

            $catalogSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'meta_robots',
                [
                    'group' => 'Search Engine Optimization',
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Meta Robots',
                    'input' => 'select',
                    'class' => '',
                    'source' => 'MageWorx\SeoBase\Model\Source\MetaRobots',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => InstallData::META_ROBOTS_DEFAULT_VALUE,
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
                    'source' => 'MageWorx\SeoBase\Model\Source\CrossDomainStore',
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
        }

        $setup->endSetup();
    }
}
