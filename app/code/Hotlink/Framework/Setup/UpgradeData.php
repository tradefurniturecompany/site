<?php
namespace Hotlink\Framework\Setup;

class UpgradeData extends \Hotlink\Framework\Setup\AbstractData implements \Magento\Framework\Setup\UpgradeDataInterface
{

    function upgrade( \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
                             \Magento\Framework\Setup\ModuleContextInterface $context )
    {
        $setup->startSetup();
        $this->_init();
        $setup->endSetup();
    }

}
