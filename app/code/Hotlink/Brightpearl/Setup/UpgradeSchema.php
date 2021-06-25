<?php
namespace Hotlink\Brightpearl\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context )
    {
        $setup->startSetup();

        if ( version_compare( $context->getVersion(), '2.5.0', '<' ) )
            {
                $this->installQueueCreditMemo( $setup, $context );
                $this->addLookupOrderStatus_OrderTypeCode( $setup, $context );
                $this->addLookupPriceListItem_PriceListTypeCode( $setup, $context );
            }

        if ( version_compare( $context->getVersion(), '2.6.0', '<' ) )
            {
                $this->addOrderReconciliationQueue_OAuth2Fields( $setup, $context );
            }

        if ( version_compare( $context->getVersion(), '2.7.0', '<' ) )
            {
                $this->addLookupWarehouse_Quarantine_Location( $setup, $context );
            }

        if ( version_compare( $context->getVersion(), '2.10.0', '<' ) )
            {
                $this->removeMsiStockSourceTable( $setup, $context );
            }

        $setup->endSetup();
    }

    protected function removeMsiStockSourceTable( $setup, $context )
    {
        $table = $setup->getTable( 'hotlink_brightpearl_msi_source_item' );
        $setup->getConnection()->dropTable( $table );
    }

    protected function addLookupWarehouse_Quarantine_Location( $setup, $context )
    {
        $setup->getConnection()->addColumn(
            $setup->getTable( 'hotlink_brightpearl_lookup_warehouse' ),
            'quarantine_location_id',
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'default'  => null,
                'comment'  => 'Quarantine location optionally used by credit memos',
            ]
        );
    }

    protected function addOrderReconciliationQueue_OAuth2Fields( $setup, $context )
    {
        $setup->getConnection()->addColumn(
            $setup->getTable( 'hotlink_brightpearl_queue_order' ),
            'sent_oauth_instance_id',
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'   => '255',
                'nullable' => true,
                'default'  => null,
                'comment'  => 'OAuth2 app instance id used when this record was sent to bp',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable( 'hotlink_brightpearl_queue_order' ),
            'reconciliation_oauth_instance_id',
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'   => '255',
                'nullable' => true,
                'default'  => null,
                'comment'  => 'OAuth2 app instance id used when attempting to reconcile this record',
            ]
        );
    }

    protected function addLookupOrderStatus_OrderTypeCode( $setup, $context )
    {
        $setup->getConnection()->addColumn(
            $setup->getTable( 'hotlink_brightpearl_lookup_order_status' ),
            'order_type_code',
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'   => '255',
                'nullable' => true,
                'default'  => null,
                'comment'  => 'Brightpearl status category',
            ]
        );
    }

    protected function addLookupPriceListItem_PriceListTypeCode( $setup, $context )
    {
        $setup->getConnection()->addColumn(
            $setup->getTable( 'hotlink_brightpearl_lookup_price_list_item' ),
            'price_list_type_code',
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'   => '255',
                'nullable' => true,
                'default'  => null,
                'comment'  => 'Describes the type of price list',
            ]
        );
    }

    protected function installQueueCreditMemo( $setup, $context )
    {
        $table = $setup->getConnection()
               ->newTable( $setup->getTable( 'hotlink_brightpearl_queue_creditmemo' ) )
               ->addColumn(
                   'id',
                   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                   null,
                   [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                   'Internal ID')
               ->addColumn(
                   'creditmemo_id',
                   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                   null,
                   [ 'unsigned' => true, 'nullable' => false, 'default' => 0 ],
                   'Magento Credit Memo Id (FK)')
               ->addColumn(
                   'send_to_bp',
                   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                   null,
                   [ 'unsigned' => true, 'nullable' => false, 'default' => '0' ],
                   'Is credit memo queued to be sent to BP? 0=false, otherwise true')

               ->addColumn(
                   'sales_credit_in_bp',
                   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                   null,
                   [ 'unsigned' => true, 'nullable' => false, 'default' => 0 ],
                   'Is sales credit in BP ? (yes, no)')
               ->addColumn(
                   'sales_credit_id',
                   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                   null,
                   [ 'unsigned' => true, 'nullable' => true, 'default' => null ],
                   'The Brightpearl sales credit id')
               ->addColumn(
                   'sales_credit_sent_at',
                   \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                   null,
                   [ 'nullable' => true, 'default' => null  ],
                   'Date/time the export of this entity was completed')

               ->addColumn(
                   'refund_in_bp',
                   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                   null,
                   [ 'unsigned' => true, 'nullable' => false, 'default' => 0 ],
                   'Is refund in BP ? (yes, no, null = not required)' )
               ->addColumn(
                   'refund_id',
                   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                   null,
                   [ 'unsigned' => true, 'nullable' => true, 'default' => null ],
                   'The Brightpearl sales credit id')
               ->addColumn(
                   'refund_sent_at',
                   \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                   null,
                   [ 'nullable' => true, 'default' => null  ],
                   'Date/time the export of this entity was completed')

               ->addColumn(
                   'quarantine_in_bp',
                   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                   null,
                   [ 'unsigned' => true, 'nullable' => false, 'default' => 0 ],
                   'Is quarantine note in BP ? (yes, no, null = not required)' )
               ->addColumn(
                   'quarantine_id',
                   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                   null,
                   [ 'unsigned' => true, 'nullable' => true, 'default' => null ],
                   'The Brightpearl bp quarantine goods note id' )
               ->addColumn(
                   'quarantine_sent_at',
                   \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                   null,
                   [ 'nullable' => true, 'default' => null  ],
                   'Date/time the export of this entity was completed')

               ->addColumn(
                   'bp_order_id',
                   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                   null,
                   [ 'unsigned' => true, 'nullable' => true, 'default' => null ],
                   'The Brightpearl order id')

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
                   'Date/time the export of this entity was completed')

               ->addColumn(
                   'sent_token',
                   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                   255,
                   [ 'nullable' => true, 'default' => null  ],
                   'Token value at the time this record was sent to bp')

               ->addIndex(
                   $setup->getIdxName(
                       $setup->getTable( 'hotlink_brightpearl_queue_credit_memo' ),
                       [ 'creditmemo_id' ],
                       \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE ),
                   [ 'creditmemo_id' ],
                   [ 'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE ] )

               ->addForeignKey(
                   $setup->getFkName(
                       $setup->getTable( 'hotlink_brightpearl_queue_credit_memo' ),
                       'creditmemo_id',
                       $setup->getTable( 'sales_creditmemo' ),
                       'entity_id' ),
                   'creditmemo_id',
                   $setup->getTable( 'sales_creditmemo' ),
                   'entity_id',
                   \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                   \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE );

        $setup->getConnection()->createTable($table);
    }

}
