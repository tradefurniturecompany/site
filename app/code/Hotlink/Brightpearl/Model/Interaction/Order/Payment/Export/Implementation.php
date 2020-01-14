<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Payment\Export;

class Implementation extends \Hotlink\Brightpearl\Model\Interaction\Order\Implementation\AbstractImplementation
{

    protected $queueOrderHelper;
    protected $queuePaymentHelper;
    protected $apiServiceWorkflowHelper;
    protected $dataOrderPaymentsFactory;
    protected $salesOrderFactory;
    protected $storeManager;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Brightpearl\Helper\Api\Service\Workflow $apiServiceWorkflowHelper,
        \Hotlink\Brightpearl\Helper\Queue\Order $queueOrderHelper,
        \Hotlink\Brightpearl\Helper\Queue\Payment $queuePaymentHelper,
        \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\PaymentsFactory $dataOrderPaymentsFactory,
        \Magento\Sales\Model\OrderFactory $salesOrderFactory
    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );
        $this->storeManager = $storeManager;
        $this->apiServiceWorkflowHelper = $apiServiceWorkflowHelper;
        $this->queueOrderHelper = $queueOrderHelper;
        $this->queuePaymentHelper = $queuePaymentHelper;
        $this->dataOrderPaymentsFactory = $dataOrderPaymentsFactory;
        $this->salesOrderFactory = $salesOrderFactory;
    }

    protected function _getName()
    {
        return 'Hotlink Brightpearl: Magento Sales Order Payment Exporter';
    }

    /**
     * Even though the initial design of this implementation was to only handle Payments,
     * later on support for Orders was added in order to accomodate a change in requirements.
     */

    public function execute()
    {
        $report = $this->getReport();
        $environment = $this->getEnvironment();
        $report( $environment, 'status' );

        $stream = $environment->getParameter( 'stream' );

        $objects = $stream->getValue();
        $paymentCount = 0;

        foreach ( $objects as $object )
            {
                if ( $object instanceof \Magento\Sales\Model\Order )
                    {
                        foreach ( $object->getPaymentsCollection() as $payment )
                            {
                                $this->_exportPayment( $payment );
                                $paymentCount++;
                            }
                    }
                else if ( $object instanceof \Magento\Sales\Model\Order\Payment )
                    {
                        $this->_exportPayment( $object );
                        $paymentCount++;
                    }
                else
                    {
                        $report->error( 'Invalid object type in stream '. get_class( $object ) );
                    }
            }
        if ( $paymentCount == 0 )
            {
                $report->debug( 'No payment to process' );
            }
        return $this;
    }

    protected function _exportPayment( \Magento\Sales\Model\Order\Payment $payment )
    {
        $report = $this->getReport();
        $api = $this->apiServiceWorkflowHelper;

        $paymentId = $payment->getId();
        $report->info( "Processing payment with id $paymentId" )->indent();

        $orderId = $payment->getParentId();
        $order = $this->getOrder( $payment );
        if ( is_null( $order ) )
            {
                $report->incFail()->error( "Unable to find order with id $orderId" );
            }
        else
            {
                $orderIncrementId = $order->getIncrementId();
                $report->info( "Identified parent order with id $orderIncrementId" );
                $report->addReference( $orderIncrementId );
                $storeId = $order->getStoreId();
                if ( is_null( $storeId ) )
                    {
                        //$storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
                        $storeId = $this->storeManager->getStore( \Magento\Store\Model\Store::ADMIN_CODE )->getId();
                        $report->warn( "Parent order with id $orderIncrementId is missing store id. This can happen when the store this order was placed from does not exist anymore. Admin store assumed in this case." );
                    }
                $environment = $this->getOrCreateEnvironment( $storeId );
                if ( $environment->isEnabled() )
                    {
                        $orderTracking = $this->queueOrderHelper->getObject( $order );
                        $paymentTracking = $this->queuePaymentHelper->getObject( $payment );

                        $report( $orderTracking, 'status', 'Order tracking information' );
                        $report( $paymentTracking, 'status', 'Payment tracking information' );

                        $send = $paymentTracking->shouldSend( $orderTracking );

                        if ( $send )
                            {
                                $inBp       = $paymentTracking->getInBp();
                                $sendToBp   = $paymentTracking->getSendToBp();
                                $sentAt     = $paymentTracking->getSentAt();
                                $lastAmount = (double) $paymentTracking->getLastAmount();
                                $success    = false;

                                //
                                // Send to BP API
                                //
                                try
                                    {

                                        list( $data, $amountPaid ) = $this->_mapPayment( $payment, $environment, $lastAmount );
                                        $result = $report( $api, 'exportOrderPayment', $storeId, $environment->getAccountCode(), $orderIncrementId, $data );
                                        $report
                                            ->incSuccess()
                                            ->info( 'Payment exported successfully' );

                                        $inBp       = true;
                                        $sendToBp   = false;
                                        $sentAt     = gmdate( \Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT );
                                        $lastAmount += $amountPaid;
                                        $success    = true;
                                    }
                                catch ( \Exception $e )
                                    {
                                        $report
                                            ->incFail()
                                            ->error( 'Failed to export payment: ' . $e->getMessage(), $e );
                                        $sendToBp = true;
                                    }

                                //
                                // Update tracking
                                //
                                $this->queuePaymentHelper->update( $paymentTracking, $inBp, $sendToBp, $sentAt, $lastAmount );
                                $report( $paymentTracking, 'status', 'Tracking information updated successfully' );
                            }
                        else
                            {
                                $report->info('Payment skipped');
                            }
                    }
                else
                    {
                        $report->debug("Interaction disabled in store $storeId");
                    }
            }
        $report->unindent();
    }

    protected function _mapPayment( \Magento\Sales\Model\Order\Payment $payment, $environment, $lastAmount )
    {
        $report = $this->getReport();

        $data = $this->dataOrderPaymentsFactory->create()->setHelper( $environment );
        $report( $data, 'map', [ $payment ], \Hotlink\Brightpearl\Model\Platform\Type::MAGEMODEL );

        $amountPaid = 0.0;
        if ( isset( $data[ 0 ] ) )
            {
                // send the difference between total amount paid and last amount sent to BP
                $amountPaid = (double) $data[0]['amountPaid'] - $lastAmount;
                $amountPaid = max($amountPaid, 0.0);

                // override mapped amountPaid
                $data[ 0 ][ 'amountPaid' ] = $amountPaid;
                $report->info("Amount mapped " . sprintf( '%0.2f', $amountPaid ) );
            }
        return [ $data, $amountPaid ];
    }

    protected function getOrder( \Magento\Sales\Model\Order\Payment $payment )
    {
        if ( $payment->getOrder() )
            {
                return $payment->getOrder();
            }

        $order = $this->salesOrderFactory->create()->load( $payment->getParentId() );
        if ( $order->getId() )
            {
                $payment->setOrder($order);
                return $order;
            }

        return null;
    }

}
