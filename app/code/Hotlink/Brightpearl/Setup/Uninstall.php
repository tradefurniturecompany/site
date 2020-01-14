<?php
namespace Hotlink\Brightpearl\Setup;

class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{

    public function uninstall( \Magento\Framework\Setup\SchemaSetupInterface $setup,
                               \Magento\Framework\Setup\ModuleContextInterface $context )
    {
        $setup->startSetup();

        $setup->getConnection()->dropTable( 'hotlink_brightpearl_lookup_channel' );
        $setup->getConnection()->dropTable( 'hotlink_brightpearl_lookup_nominal_code' );
        $setup->getConnection()->dropTable( 'hotlink_brightpearl_lookup_warehouse' );
        $setup->getConnection()->dropTable( 'hotlink_brightpearl_lookup_order_status' );
        $setup->getConnection()->dropTable( 'hotlink_brightpearl_lookup_order_field' );
        $setup->getConnection()->dropTable( 'hotlink_brightpearl_lookup_price_list_item' );
        $setup->getConnection()->dropTable( 'hotlink_brightpearl_lookup_shipping_method' );
        $setup->getConnection()->dropTable( 'hotlink_brightpearl_lookup_order_custom_field' );

        $setup->getConnection()->dropTable( 'hotlink_brightpearl_stock_item' );

        $setup->getConnection()->dropTable( 'hotlink_brightpearl_queue_order' );
        $setup->getConnection()->dropTable( 'hotlink_brightpearl_queue_payment' );
        $setup->getConnection()->dropTable( 'hotlink_brightpearl_queue_order_status' );

        $setup->getConnection()->dropTable( 'hotlink_brightpearl_shipment' );

        // >= 2.5.0
        $setup->getConnection()->dropTable( 'hotlink_brightpearl_queue_creditmemo' );

        $setup->endSetup();
    }
}
