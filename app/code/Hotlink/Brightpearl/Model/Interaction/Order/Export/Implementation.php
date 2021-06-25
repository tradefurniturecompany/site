<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Export;

class Implementation extends \Hotlink\Brightpearl\Model\Interaction\Order\Implementation\AbstractImplementation
{

    protected $apiServiceWorkflowHelper;
    protected $eventManager;
    protected $dataOrderExportFactory;
    protected $storeManager;
    protected $queueOrderHelper;
    protected $customisation;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Brightpearl\Helper\Api\Service\Workflow $apiServiceWorkflowHelper,
        \Hotlink\Brightpearl\Helper\Queue\Order $queueOrderHelper,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\ExportFactory $dataOrderExportFactory,
        \Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper\Customisation $customisation
    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );
        $this->storeManager = $storeManager;
        $this->apiServiceWorkflowHelper = $apiServiceWorkflowHelper;
        $this->queueOrderHelper = $queueOrderHelper;
        $this->eventManager = $eventManager;
        $this->dataOrderExportFactory = $dataOrderExportFactory;
        $this->customisation = $customisation;
    }

    protected function _getName()
    {
        return 'Hotlink Brightpearl: Magento Sales Order Exporter';
    }

    public function execute()
    {
        $report = $this->getReport();
        $environment = $this->getEnvironment();
        $legacyToken = null;
        $oauthInstanceId = null;
        if ( $environment->isOAuth2Active() )
            {
                $oauthInstanceId = $environment->getOAuth2InstanceId();
            }
        else
            {
                $legacyToken = $environment->getLegacyToken();
            }
        $report( $environment, 'status' );

        // force parameter only exists when interaction is triggered manually (from admin screen)
        $isForced = ($forcedParam = $environment->getParameter( 'force' ))
            ? $forcedParam->getValue()
            : false;

        $stream = $environment->getParameter('stream');
        $orders = $stream->getValue();
        $orderCount = 0;

        foreach ( $orders as $order )
            {
                $incrementId = $order->getIncrementId();
                $storeId = $order->getStoreId();
                $orderCount++;

                if ( is_null( $storeId ) )
                    {
                        if ( $isForced )
                            {
                                // order is missing store_id. use admin if order export is forced.
                                // @see else case, read the error message.

                                //$storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
                                $storeId = $this->storeManager->getStore( \Magento\Store\Model\Store::ADMIN_CODE )->getId();
                                $report->debug( "Order [$incrementId] is missing store id. Since export is forced admin store is assumed." );
                            }
                        else
                            {
                                $report
                                    ->incFail()
                                    ->error( "Order [$incrementId] is missing store id. This can happen when the store this order was placed from does not exist anymore. In order to export this order please force export it manually." );
                                continue;
                            }
                    }

                $environment = $this->getOrCreateEnvironment($storeId);

                if ( $environment->isEnabled() )
                    {
                        $report->addReference( $incrementId );
                        $report
                            ->info( "Processing order with id $incrementId" )
                            ->indent()
                            ->info( 'Order shipping_method = "'. $order->getShippingMethod() .'"' );

                        $orderTracking = $this->queueOrderHelper->getObject( $order );
                        $report( $orderTracking, 'status', 'Order tracking information' );

                        // ignore tracking flags when export is forced
                        $send = $isForced ? true : $orderTracking->shouldSend();
                        if ( $send )
                            {
                                $inBp      = $orderTracking->getInBp();
                                $sendToBp  = $orderTracking->getSendToBp();
                                $sentAt    = $orderTracking->getSentAt();
                                $sentToken = $orderTracking->getSentToken();
                                $sentOauthInstanceId = $orderTracking->getSentOauthInstanceId();
                                $success   = false;

                                //
                                // Send to BP API
                                //
                                try
                                    {
                                        $orderData = $this->_mapOrder( $order, $environment );
                                        $report( $this->apiServiceWorkflowHelper, 'exportOrder',
                                                 $storeId, $environment->getAccountCode(), $orderData );
                                        $report
                                            ->incSuccess()
                                            ->info('Order exported successfully');
                                        $inBp      = true;
                                        $sendToBp  = false;
                                        $sentAt    = gmdate( \Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT );
                                        $sentToken = $legacyToken;
                                        $sentOauthInstanceId = $oauthInstanceId;
                                        $success   = true;
                                    }
                                catch ( \Exception $e )
                                    {
                                        if ( $e->getCode() == 2171828 )
                                            {
                                                $e = false;
                                            }
                                        $report->incFail()->error( 'Unable to export order', $e );
                                        if ( !$isForced )
                                            {
                                                $sendToBp = true;
                                            }
                                    }

                                //
                                // Update tracking
                                //
                                $this->queueOrderHelper->update( $orderTracking, $inBp, $sendToBp, $sentAt, $sentToken, $sentOauthInstanceId );
                                $report( $orderTracking, 'status', 'Tracking information updated successfully' );

                                //
                                // Notify observers
                                //
                                if ( $success )
                                    {
                                        // Trigger event after a successful order export, so that Payment Export trigger can
                                        // export the payment associated with this order.
                                        // This synchronisation is important as Brightpearl would not accept
                                        // a payment unless the order is exported first.
                                        $report->debug( 'Dispatching hotlink_brightpearl_order_exported event' );
                                        try
                                            {
                                                $this->eventManager->dispatch( 'hotlink_brightpearl_order_exported', [ 'order' => $order ] );
                                            }
                                        catch ( \Exception $e )
                                            {
                                                // in case of failure this interaction continues to process the rest of the orders
                                                $report->warn( "There was an error dispatching the event, please see the payment export log for details:". $e->getMessage(), $e );
                                            }
                                    }
                            }
                        else
                            {
                                $report->info( "Order skipped" );
                            }
                    }
                else
                    {
                        $report->debug( "Interaction disabled in store $storeId" );
                    }
            $report->unindent();
        }

        if ( 0 === $orderCount )
            {
                $report->debug('No order to process');
            }

        return $this;
    }

    protected function _mapOrder( \Magento\Sales\Model\Order $order,
                                  \Hotlink\Framework\Model\Interaction\Environment\AbstractEnvironment $environment )
    {
        $report = $this->getReport();
        $data = $this->dataOrderExportFactory->create();
        $data->setHelper( $environment );

        $report( $data, 'map', $order, \Hotlink\Brightpearl\Model\Platform\Type::MAGEMODEL );

        $config = $environment->getConfig();
        $storeId = $environment->getStoreId();
        $customisationMap = $config->getCustomisationMap( $storeId );

        $report->debug( "Applying customisations for store [$storeId]" );
        $report( $this->getCustomisation(), 'apply', $customisationMap, $order, $data );
        return $data;
    }

    public function getCustomisation()
    {
        return $this->customisation;
    }

}
