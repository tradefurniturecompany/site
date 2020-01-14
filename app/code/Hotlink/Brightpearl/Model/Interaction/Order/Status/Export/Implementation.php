<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Export;

class Implementation extends \Hotlink\Brightpearl\Model\Interaction\Order\Implementation\AbstractImplementation
{

    /**
     * @var \Hotlink\Brightpearl\Helper\Api\Service\Workflow
     */
    protected $brightpearlApiServiceWorkflowHelper;

    /**
     * @var \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Status\ExportFactory
     */
    protected $orderStatusPlatformDataFactory;

    protected $storeManager;

    protected $queueOrderHelper;
    protected $queueOrderStatusHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Brightpearl\Helper\Api\Service\Workflow $brightpearlApiServiceWorkflowHelper,
        \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Status\ExportFactory $orderStatusPlatformDataFactory,
        \Hotlink\Brightpearl\Helper\Queue\Order $queueOrderHelper,
        \Hotlink\Brightpearl\Helper\Queue\Order\Status $queueOrderStatusHelper
    ) {
        $this->storeManager = $storeManager;
        $this->brightpearlApiServiceWorkflowHelper = $brightpearlApiServiceWorkflowHelper;
        $this->orderStatusPlatformDataFactory = $orderStatusPlatformDataFactory;
        $this->queueOrderHelper = $queueOrderHelper ;
        $this->queueOrderStatusHelper = $queueOrderStatusHelper;

        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );
    }

    protected function _getName()
    {
        return 'Hotlink Brightpearl: Magento Sales Order Status Exporter';
    }

    public function execute()
    {
        $report = $this->getReport();
        $environment = $this->getEnvironment();
        $api = $this->brightpearlApiServiceWorkflowHelper;
        $report->__invoke($environment, 'status');

        $stream = $environment->getParameter( 'stream' );
        $orders = $stream->getValue();
        $orderCount = 0;

        foreach ($orders as $order) {
            $orderIncrementId = $order->getIncrementId();
            $storeId = $order->getStoreId();

            if (is_null($storeId)) {
                $storeId = $this->storeManager->getStore( \Magento\Store\Model\Store::ADMIN_CODE )->getId();
                $report->warn("Order with id $orderIncrementId is missing store id. This can happen when the store this order was placed from does not exist anymore. Admin store assumed in this case.");
            }

            $environment = $this->getOrCreateEnvironment($storeId);
            $orderCount++;

            if ($environment->isEnabled()) {
                $orderId = $order->getId();
                $report->addReference($orderIncrementId);

                $report
                    ->info("Processing order with id $orderIncrementId")
                    ->indent()
                    ->info('Order status = "'. $order->getStatus() .'"');

                //$orderTracking = $this->brightpearlTrackingHelper->getOrderTracking($order);
                //$statusTracking = $this->brightpearlTrackingHelper->getStatusTracking($order);

                $orderTracking  = $this->queueOrderHelper->getObject( $order );
                $statusTracking = $this->queueOrderStatusHelper->getObject( $order );

                /* $this->reportTracking("Order tracking information", $orderTracking); */
                /* $this->reportTracking("Status tracking information", $statusTracking); */
                $report( $orderTracking, 'status', 'Order tracking information' );
                $report( $statusTracking, 'status', 'Status tracking information' );

                //$send = $this->shouldSend($statusTracking, $orderTracking);
                if ( $statusTracking->shouldSend( $orderTracking ) ) {

                    $inBp     = $statusTracking->getInBp();
                    $sendToBp = $statusTracking->getSendToBp();
                    $sentAt   = $statusTracking->getSentAt();
                    $success  = false;

                    //
                    // Send to BP API
                    //

                    try {
                        $data = $this->_mapStatus( $order, $environment );
                        $report( $api,
                                 'exportOrderStatus',
                                 $storeId,
                                 $environment->getAccountCode(),
                                 $orderIncrementId,
                                 $data );

                        $report
                            ->incSuccess()
                            ->info('Order status exported successfully');

                        $inBp     = true;
                        $sendToBp = false;
                        $sentAt   = gmdate( \Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT );
                        $success  = true;
                    }
                    catch ( \Exception $e) {
                        $report->error('Unable to export status: '.$e->getMessage(), $e)->incFail();
                        $sendToBp = true;
                    }

                    //
                    // Update tracking
                    //
                    //$this->updateTracking($statusTracking, $inBp, $sendToBp, $sentAt);
                    //$this->reportTracking("Tracking information updated successfully", $statusTracking);
                    $this->queueOrderStatusHelper->update( $statusTracking, $inBp, $sendToBp, $sentAt );
                    $report( $statusTracking, 'status', 'Tracking information updated successfully' );
                }
                else {
                    $report->info('Order skipped');
                }

                $report->unindent();
            }
            else {
                $report->debug("Interaction disabled in store $storeId");
            }
        }

        if ($orderCount === 0) {
            $report->debug('No order to process');
        }

        return $this;
    }

    protected function _mapStatus( \Magento\Sales\Model\Order $order, $environment)
    {
        $report = $this->getReport();
        $data = $this->orderStatusPlatformDataFactory->create()->setHelper($environment);
        return $report( $data, 'map', $order, \Hotlink\Brightpearl\Model\Platform\Type::MAGEMODEL );
    }

    /* protected function shouldSend( \Hotlink\Brightpearl\Model\Status $statusTracking, */
    /*                                \Hotlink\Brightpearl\Model\Order $orderTracking ) */
    /* { */
    /*     $send = false; */

    /*     if ($orderTracking->getInBp()) { */

    /*         if ($statusTracking->getId()) { */
    /*             if ($statusTracking->getSendToBp()) { */
    /*                 $send = true; */
    /*             } */
    /*         } */
    /*         else { */
    /*             $send = true; */
    /*         } */
    /*     } */

    /*     return $send; */
    /* } */
}
