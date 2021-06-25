<?php
namespace Hotlink\Brightpearl\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

    public function install( \Magento\Framework\Setup\SchemaSetupInterface $setup,
                             \Magento\Framework\Setup\ModuleContextInterface $context )
    {
        $setup->startSetup();

        $this->installLookupNominalCode( $setup, $context );
        $this->installLookupWarehouse( $setup, $context );
        $this->installLookupOrderStatus( $setup, $context );
        $this->installLookupChannel( $setup, $context );
        $this->installLookupPriceListItem( $setup, $context );
        $this->installLookupShippingMethod( $setup, $context );
        $this->installLookupOrderCustomField( $setup, $context );

        $this->installStockItem( $setup, $context );

        $this->installQueueOrder( $setup, $context );
        $this->installQueuePayment( $setup, $context );
        $this->installQueueOrderStatus( $setup, $context );

        $this->installShipment( $setup, $context );

        $setup->endSetup();
    }

    protected function installLookupNominalCode( $setup, $context )
    {
        $table = $setup->getConnection()
            ->newTable( $setup->getTable( 'hotlink_brightpearl_lookup_nominal_code' ) )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                'Internal ID')
            ->addColumn(
                'brightpearl_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => false ],
                'Brightpearl internal ID')
            ->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => true ],
                'Nominal code')
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => false ],
                'Nominal code name')
            ->addColumn(
                'deleted',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'unsigned'  => true, 'nullable' => false, 'default' => 0 ],
                'Deleted flag');

        $setup->getConnection()->createTable($table);
    }

    protected function installLookupWarehouse( $setup, $context )
    {
        $table = $setup->getConnection()
            ->newTable( $setup->getTable( 'hotlink_brightpearl_lookup_warehouse' ) )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                'Internal ID')
            ->addColumn(
                'brightpearl_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => false ],
                'Brightpearl internal ID')
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => false ],
                'Warehouse name')
            ->addColumn(
                'deleted',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'unsigned'  => true, 'nullable' => false, 'default' => 0 ],
                'Deleted flag');

        $setup->getConnection()->createTable($table);
    }

    protected function installLookupOrderStatus( $setup, $context )
    {
        $table = $setup->getConnection()
            ->newTable( $setup->getTable( 'hotlink_brightpearl_lookup_order_status' ) )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                'Internal ID')
            ->addColumn(
                'brightpearl_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => false ],
                'Brightpearl internal ID')
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => false ],
                'Order status name')
            ->addColumn(
                'deleted',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'unsigned'  => true, 'nullable' => false, 'default' => 0 ],
                'Deleted flag');

        $setup->getConnection()->createTable($table);
    }

    protected function installLookupChannel( $setup, $context )
    {
        $table = $setup->getConnection()
            ->newTable( $setup->getTable( 'hotlink_brightpearl_lookup_channel' ) )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                'Internal ID')
            ->addColumn(
                'brightpearl_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => false ],
                'Brightpearl internal ID')
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => false ],
                'Product channel name')
            ->addColumn(
                'deleted',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'unsigned'  => true, 'nullable' => false, 'default' => 0 ],
                'Deleted flag');

        $setup->getConnection()->createTable($table);
    }

    protected function installLookupPriceListItem( $setup, $context )
    {
        $table = $setup->getConnection()
            ->newTable( $setup->getTable( 'hotlink_brightpearl_lookup_price_list_item' ) )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                'Internal ID')
            ->addColumn(
                'brightpearl_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => false ],
                'Brightpearl internal ID')
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => false ],
                'Price List Item name')
            ->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => true ],
                'Price List Item code')
            ->addColumn(
                'currency_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => true, 'default'  => null ],
                'Price List Item currency code')
            ->addColumn(
                'deleted',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'unsigned'  => true, 'nullable' => false, 'default' => 0 ],
                'Deleted flag');

        $setup->getConnection()->createTable($table);
    }

    protected function installLookupShippingMethod( $setup, $context )
    {
        $table = $setup->getConnection()
            ->newTable( $setup->getTable( 'hotlink_brightpearl_lookup_shipping_method' ) )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                'Internal ID')
            ->addColumn(
                'brightpearl_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => false ],
                'Brightpearl internal ID')
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => false ],
                'Shipping Method name')
            ->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => true ],
                'Shipping Method code')
            ->addColumn(
                'deleted',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'unsigned'  => true, 'nullable' => false, 'default' => 0 ],
                'Deleted flag');

        $setup->getConnection()->createTable($table);
    }

    protected function installLookupOrderCustomField( $setup, $context )
    {
        $table = $setup->getConnection()
            ->newTable( $setup->getTable( 'hotlink_brightpearl_lookup_order_custom_field' ) )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                'Internal ID')
            ->addColumn(
                'brightpearl_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => false ],
                'Brightpearl internal ID')
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => false ],
                'Order Field name')
            ->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => true, 'default' => null ],
                'Order Field code')
            ->addColumn(
                'deleted',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'unsigned'  => true, 'nullable' => false, 'default' => 0 ],
                'Deleted flag');

        $setup->getConnection()->createTable($table);
    }

    protected function installStockItem( $setup, $context )
    {
        $table = $setup->getConnection()
            ->newTable( $setup->getTable( 'hotlink_brightpearl_stock_item' ) )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                'Internal ID')
            ->addColumn(
                'item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'nullable' => false, 'primary' => true ],
                'Corresponds with cataloginventory_stock_item.item_id')
            ->addColumn(
                'timestamp',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => true, 'default' => null ],
                'When Brightpearl was last checked for this item')
            ->addColumn(
                'brightpearl_level',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => true, 'default' => null ],
                'Brightpearl stock level');

        $setup->getConnection()->createTable($table);
    }

    protected function installQueueOrder( $setup, $context )
    {
        $table = $setup->getConnection()
            ->newTable( $setup->getTable( 'hotlink_brightpearl_queue_order' ) )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                'Internal ID')
            ->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => false, 'default' => 0 ],
                'Magento Sales Order Id (FK)')
            ->addColumn(
                'send_to_bp',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'unsigned' => true, 'nullable' => false, 'default' => '0' ],
                'Is order queued to be sent to BP? 0=false, otherwise true')
            ->addColumn(
                'in_bp',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'unsigned' => true, 'nullable' => false, 'default' => 0 ],
                'Is order is BP ?')
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [ 'nullable' => false  ],
                'Date/time of creation of this record')
            ->addColumn(
                'sent_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [ 'nullable' => true, 'default' => null  ],
                'Date/time this order has been sent to BP via Order Export')
            ->addColumn(
                'sent_token',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => true, 'default' => null  ],
                'Token value at the time this record was sent to bp')
            ->addColumn(
                'reconciled_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [ 'nullable' => true, 'default' => null ],
                'Date/time this order has been reconciled with BP' )
            ->addColumn(
                'reconciliation_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => true, 'default' => null  ],
                'Reconciliation status')
            ->addColumn(
                'reconciliation_token',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => true, 'default' => null  ],
                'Token value at the time this record was reconciled')

            ->addIndex( $setup->getIdxName( 'hotlink_brightpearl_queue_order',
                                            [ 'order_id' ],
                                            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE ),
                        [ 'order_id' ],
                        [ 'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE ] )

            ->addForeignKey(
                $setup->getFkName(
                    $setup->getTable( 'hotlink_brightpearl_queue_order' ),
                    'order_id',
                    $setup->getTable( 'sales_order' ),
                    'entity_id' ),
                'order_id',
                $setup->getTable( 'sales_order' ),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE );

        $setup->getConnection()->createTable($table);
    }

    protected function installQueuePayment( $setup, $context )
    {
        $table = $setup->getConnection()
            ->newTable( $setup->getTable( 'hotlink_brightpearl_queue_payment' ) )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                'Internal ID')
            ->addColumn(
                'payment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => false ],
                'Magento Sales Order Payment Id (FK)')
            ->addColumn(
                'send_to_bp',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'unsigned' => true, 'nullable' => false, 'default' => '0' ],
                'Is payment queued to be sent to BP?')
            ->addColumn(
                'in_bp',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'unsigned' => true, 'nullable' => false, 'default' => 0 ],
                'Is payment is BP ?')
            ->addColumn(
                'last_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                [ 'unsigned' => true, 'nullable' => false, 'default'   => '0.0000' ],
                'Last cumulative amount as sent to BP to date, used to calculate difference to send to BP')
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [ 'nullable' => false  ],
                'Date/time of creation of this record')
            ->addColumn(
                'sent_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [ 'nullable' => true, 'default' => null  ],
                'Date/time this payment has been sent to BP via Order Export')

            ->addIndex( $setup->getIdxName( 'hotlink_brightpearl_queue_payment',
                                            [ 'payment_id' ],
                                            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE ),
                        [ 'payment_id' ],
                        [ 'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE ] )

            ->addForeignKey(
                $setup->getFkName(
                    $setup->getTable( 'hotlink_brightpearl_queue_payment' ),
                    'payment_id',
                    $setup->getTable( 'sales_order_payment' ),
                    'entity_id' ),
                'payment_id',
                $setup->getTable( 'sales_order_payment' ),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE );

        $setup->getConnection()->createTable($table);
    }

    protected function installQueueOrderStatus( $setup, $context )
    {
        $table = $setup->getConnection()
            ->newTable( $setup->getTable( 'hotlink_brightpearl_queue_order_status' ) )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                'Internal ID')
            ->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => false ],
                'Magento Sales Order Id (FK)' )
            ->addColumn(
                'send_to_bp',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'unsigned'  => true, 'nullable'  => false, 'default' => 0  ],
                'Is order status queued to be sent to BP?' )
            ->addColumn(
                'in_bp',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => false, 'default' => 0 ],
                'Is order status queued to be sent to BP? 0=false, otherwise true; also acts as a counter' )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [ 'nullable' => false ],
                'Date/time of creation of this record' )
            ->addColumn(
                'sent_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [ 'nullable'  => true, 'default' => null ],
                'Date/time this order status has been sent to BP via Order Status Export' )

            ->addIndex( $setup->getIdxName( 'hotlink_brightpearl_queue_order_status',
                                            [ 'order_id' ],
                                            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE ),
                        [ 'order_id' ],
                        [ 'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE ] )

            ->addForeignKey(
                $setup->getFkName(
                    $setup->getTable( 'hotlink_brightpearl_queue_order_status' ),
                    'order_id',
                    $setup->getTable( 'sales_order' ),
                    'entity_id' ),
                'order_id',
                $setup->getTable( 'sales_order' ),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE );

        $setup->getConnection()->createTable($table);
    }

    protected function installShipment( $setup, $context )
    {
        $table = $setup->getConnection()
            ->newTable( $setup->getTable( 'hotlink_brightpearl_shipment' ) )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                'Internal ID')
            ->addColumn(
                'brightpearl_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned'  => true, 'nullable'  => false ],
                'Brightpearl internal ID' )
            ->addColumn(
                'shipment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned'  => true, 'nullable'  => false ],
                'Magento Shipment Id' )
            ->addColumn(
                'shipment_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable'  => false ],
                'Brightpearl shipment note type' )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [ 'nullable'  => false ],
                'Date/time of creation of this record' )

            ->addIndex( $setup->getIdxName( 'hotlink_brightpearl_shipment',
                                            array( 'brightpearl_id' ),
                                            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX ),
                        [ 'brightpearl_id' ],
                        [ 'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX ] )
            ->addForeignKey(
                $setup->getFkName(
                    $setup->getTable( 'hotlink_brightpearl_shipment' ),
                    'shipment_id',
                    $setup->getTable( 'sales_shipment' ),
                    'entity_id' ),
                'shipment_id',
                $setup->getTable( 'sales_shipment' ),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE )
            ->addIndex( $setup->getIdxName( 'hotlink_brightpearl_shipment',
                                            [ 'shipment_id', 'shipment_type' ],
                                            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE ),
                        [ 'shipment_id', 'shipment_type' ],
                        [ 'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE ] );


        $setup->getConnection()->createTable($table);
    }

}