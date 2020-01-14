<?php
namespace Magesales\Shippingtable\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
	
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
	
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        //$setup->startSetup();
  		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
		$eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'shipping_type',
            [
                'group' => 'General',
        		'label' => 'Shipping Type',
				'type'  => 'varchar',
        		'input' => 'select',
        		'source' => '',
                'required' => false,
                'sort_order' => 150,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => false
            ]
        );
		
        $setup->endSetup();
    }
}