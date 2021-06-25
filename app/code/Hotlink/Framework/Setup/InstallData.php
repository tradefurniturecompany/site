<?php
namespace Hotlink\Framework\Setup;

class InstallData extends \Hotlink\Framework\Setup\AbstractData implements \Magento\Framework\Setup\InstallDataInterface
{

    public function install( \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
                             \Magento\Framework\Setup\ModuleContextInterface $context )
    {
        $setup->startSetup();
        $this->_init();
        $setup->endSetup();
    }

}
