<?php
namespace Hotlink\Framework\Setup;

class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{

    public function uninstall( \Magento\Framework\Setup\SchemaSetupInterface $setup,
                               \Magento\Framework\Setup\ModuleContextInterface $context )
    {
        $setup->startSetup();

        $table = $setup->getConnection()->dropTable( $setup->getTable( 'hotlink_framework_report_log' ) );

        $setup->endSetup();
    }

}
