<?php
namespace Hotlink\Brightpearl\Model\Monitor\Order\Status;

class Queue extends \Hotlink\Brightpearl\Model\Monitor\Queue\AbstractQueue
{

    /**
     * This monitor is responsible with (re)sending failed order statuses to Brightpearl.
     */

    protected $hotlinkResourceOrderStatusFactory;
    protected $salesResourceModelOrderCollectionFactory;

    function __construct(
        \Hotlink\Framework\Model\ReportFactory $reportFactory,
        \Hotlink\Framework\Helper\Convention\Monitor $conventionMonitorHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Magento\Framework\Event\ManagerInterface $eventManager,

        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesResourceModelOrderCollectionFactory,
        \Hotlink\Brightpearl\Model\ResourceModel\Queue\Order\StatusFactory $hotlinkResourceOrderStatusFactory
    ) {
        $this->salesResourceModelOrderCollectionFactory = $salesResourceModelOrderCollectionFactory;
        $this->hotlinkResourceOrderStatusFactory = $hotlinkResourceOrderStatusFactory;

        parent::__construct(
            $reportFactory,
            $conventionMonitorHelper,
            $factoryHelper,
            $interaction,
            $configMap,
            $eventManager
        );
    }

    function getCronFieldName()
    {
        return 'monitor_order_status_queue_cron_expr';
    }

    protected function _getName()
    {
        return 'Order Status Queue Monitor';
    }

    function execute()
    {
        $this->_process( 'hotlink_framework_monitor_order_status_queue' );
    }

    function getList()
    {
        $config = $this->getConfig();

        $hotlinkResourceStatus = $this->hotlinkResourceOrderStatusFactory->create();
        $hotlinkStatusTable = $hotlinkResourceStatus->getMainTable();


        $collection = $this->salesResourceModelOrderCollectionFactory->create();

        $collection->join( [ 'bpord' => $hotlinkStatusTable ],
                           'main_table.entity_id=bpord.order_id',
                           [ 'send_to_bp_flag' => 'bpord.send_to_bp',
                             'queued_at'       => 'bpord.created_at']  );

        $collection->addFieldToFilter('bpord.send_to_bp', array('eq' => '1'));
        if ( count( $this->getProcessed() ) > 0 ) {
            $collection->addFieldToFilter( 'main_table.entity_id', [ 'nin' => $this->getProcessed() ] );
        }
        $sortField = $config->getSortField();
        $sortOrder = $config->getSortOrder();
        $collection->setOrder($sortField, $sortOrder);
        return $collection;
    }
}