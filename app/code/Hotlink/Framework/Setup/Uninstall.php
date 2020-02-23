<?php
namespace Hotlink\Framework\Setup;

class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{

    function uninstall( \Magento\Framework\Setup\SchemaSetupInterface $setup,
                               \Magento\Framework\Setup\ModuleContextInterface $context )
    {
        $setup->startSetup();

        $table = $setup->getConnection()->dropTable( 'hotlink_framework_report_log' );

        $setup->endSetup();
    }

}
