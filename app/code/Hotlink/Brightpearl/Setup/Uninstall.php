<?php
namespace Hotlink\Brightpearl\Setup;

class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{

    function uninstall( \Magento\Framework\Setup\SchemaSetupInterface $setup,
                               \Magento\Framework\Setup\ModuleContextInterface $context )
    {
        $setup->startSetup();

        $tables
            =
            [ 'hotlink_brightpearl_lookup_channel',
              'hotlink_brightpearl_lookup_nominal_code',
              'hotlink_brightpearl_lookup_warehouse',
              'hotlink_brightpearl_lookup_order_status',
              'hotlink_brightpearl_lookup_order_field',
              'hotlink_brightpearl_lookup_price_list_item',
              'hotlink_brightpearl_lookup_shipping_method',
              'hotlink_brightpearl_lookup_order_custom_field',
              'hotlink_brightpearl_stock_item',
              'hotlink_brightpearl_queue_order',
              'hotlink_brightpearl_queue_payment',
              'hotlink_brightpearl_queue_order_status',
              'hotlink_brightpearl_shipment',
              'hotlink_brightpearl_queue_creditmemo',
              'hotlink_brightpearl_msi_stock_item'
            ];

        foreach ( $tables as $table )
            {
                $setup->getConnection()->dropTable( $setup->getTable( $table ) );
            }

        $setup->endSetup();
    }

}
