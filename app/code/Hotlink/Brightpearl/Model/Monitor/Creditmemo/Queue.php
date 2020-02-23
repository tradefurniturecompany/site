<?php
namespace Hotlink\Brightpearl\Model\Monitor\Creditmemo;

class Queue extends \Hotlink\Brightpearl\Model\Monitor\Queue\AbstractQueue
{

    /**
     * This monitor is responsible with (re)sending failed order statuses to Brightpearl.
     */

    protected $resourceCreditmemoCollectionFactory;
    protected $resourceQueueFactory;

    function __construct(
        \Hotlink\Framework\Model\ReportFactory $reportFactory,
        \Hotlink\Framework\Helper\Convention\Monitor $conventionMonitorHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Magento\Framework\Event\ManagerInterface $eventManager,

        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $resourceCreditmemoCollectionFactory,
        \Hotlink\Brightpearl\Model\ResourceModel\Queue\CreditmemoFactory $resourceQueueFactory
    )
    {
        $this->resourceCreditmemoCollectionFactory = $resourceCreditmemoCollectionFactory;
        $this->resourceQueueFactory = $resourceQueueFactory;

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
        return 'monitor_creditmemo_queue_cron_expr';
    }

    protected function _getName()
    {
        return 'Creditmemo Queue Monitor';
    }

    function execute()
    {
        $this->_process( 'hotlink_framework_monitor_creditmemo_queue' );
    }

    function getList()
    {
        $config = $this->getConfig();

        $queue = $this->resourceQueueFactory->create();

        $collection = $this->resourceCreditmemoCollectionFactory->create();

        $collection->join( [ 'q' => $queue->getMainTable() ],
                           'main_table.entity_id = q.creditmemo_id',
                           [ 'send_to_bp' => 'q.send_to_bp',
                             'queued_at'  => 'q.created_at' ] );

        $collection->addFieldToFilter( 'q.send_to_bp', array('eq' => '1') );

        if ( count( $this->getProcessed() ) > 0 )
            {
                $collection->addFieldToFilter( 'main_table.entity_id', [ 'nin' => $this->getProcessed() ] );
            }
        $sortOrder = $config->getSortOrder();
        $collection->setOrder( 'entity_id', $sortOrder );
        return $collection;
    }

}