<?php
namespace Hotlink\Brightpearl\Model\Monitor\Order;

class Queue extends \Hotlink\Brightpearl\Model\Monitor\Queue\AbstractQueue
{

    /**
     *  This monitor is responsible with re-sending failed order exports to Brightpearl.
     */

    protected $resourceOrderCollectionFactory;
    protected $hotlinkResourceOrderFactory;

    public function __construct(
        \Hotlink\Framework\Model\ReportFactory $reportFactory,
        \Hotlink\Framework\Helper\Convention\Monitor $conventionMonitorHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Magento\Framework\Event\ManagerInterface $eventManager,

        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $resourceOrderCollectionFactory,
        \Hotlink\Brightpearl\Model\ResourceModel\Queue\OrderFactory $hotlinkResourceOrderFactory
    )
    {
        $this->resourceOrderCollectionFactory = $resourceOrderCollectionFactory;
        $this->hotlinkResourceOrderFactory = $hotlinkResourceOrderFactory;
        parent::__construct(
            $reportFactory,
            $conventionMonitorHelper,
            $factoryHelper,
            $interaction,
            $configMap,
            $eventManager
        );
    }

    public function getCronFieldName()
    {
        return 'monitor_order_queue_cron_expr';
    }

    protected function _getName()
    {
        return 'Order Queue Monitor';
    }

    public function execute()
    {
        $this->_process( 'hotlink_framework_monitor_order_queue' );
    }

    //
    //  select orders with an associated queue item and marked to be sent, which have not already been processed
    //
    public function getList()
    {
        $collection = $this->resourceOrderCollectionFactory->create();
        $hotlinkResourceOrder = $this->hotlinkResourceOrderFactory->create();
        $hotlinkOrderTable = $hotlinkResourceOrder->getMainTable();
        $collection->join( [ 'qord' => $hotlinkOrderTable ],
                           'main_table.entity_id=qord.order_id',
                           [ 'send_to_bp_flag' => 'qord.send_to_bp',
                             'queued_at'       => 'qord.created_at' ] );
        $collection->addFieldToFilter( 'qord.send_to_bp', [ 'eq' => '1' ] );
        if ( count( $this->getProcessed() ) > 0 )
            {
                $collection->addFieldToFilter( 'main_table.entity_id', [ 'nin' => $this->getProcessed() ] );
            }
        $sortField = $this->getConfig()->getSortField();
        $sortOrder = $this->getConfig()->getSortOrder();
        $collection->setOrder( $sortField, $sortOrder );
        return $collection;
    }

}
