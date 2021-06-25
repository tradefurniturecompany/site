<?php
namespace Hotlink\Brightpearl\Model\Monitor\Order\Payment;

class Queue extends \Hotlink\Brightpearl\Model\Monitor\Queue\AbstractQueue
{

    /**
     * This monitor is responsible with (re)sending failed payments exports to Brightpearl
     */

    protected $hotlinkResourcePaymentFactory;
    protected $resourcePaymentCollectionFactory;

    function __construct(
        \Hotlink\Framework\Model\ReportFactory $reportFactory,
        \Hotlink\Framework\Helper\Convention\Monitor $conventionMonitorHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Magento\Framework\Event\ManagerInterface $eventManager,

        \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $resourcePaymentCollectionFactory,
        \Hotlink\Brightpearl\Model\ResourceModel\Queue\PaymentFactory $hotlinkResourcePaymentFactory
    )
    {
        $this->resourcePaymentCollectionFactory = $resourcePaymentCollectionFactory;
        $this->hotlinkResourcePaymentFactory = $hotlinkResourcePaymentFactory;
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
        return 'monitor_order_payment_queue_cron_expr';
    }

    protected function _getName()
    {
        return 'Payment Queue Monitor';
    }

    function execute()
    {
        $this->_process( 'hotlink_framework_monitor_order_payment_queue' );
    }

    //
    //  select payments with an associated queue item and marked to be sent, which have not already been processed
    //
    function getList()
    {
        $hotlinkResourcePayment = $this->hotlinkResourcePaymentFactory->create();
        $hotlinkPaymentTable = $hotlinkResourcePayment->getMainTable();

        $collection = $this->resourcePaymentCollectionFactory->create();
        $collection->join( [ 'bppq' => $hotlinkPaymentTable ],
                           'main_table.entity_id = bppq.payment_id',
                           [ 'send_to_bp' => 'bppq.send_to_bp',
                             'queued_at'  => 'bppq.created_at' ] );
        $collection->addFieldToFilter( 'bppq.send_to_bp', [ 'eq' => '1' ] );
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