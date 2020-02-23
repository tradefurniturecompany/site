<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation;

class Implementation extends \Hotlink\Framework\Model\Interaction\Implementation\AbstractImplementation
{

    const STATUS_INSTANCE_MISMATCH = 'INSTANCE_MISMATCH';
    const STATUS_TOKEN_MISMATCH    = 'TOKEN_MISMATCH';
    const STATUS_WITH_ERROR        = 'WITH_ERROR';
    const STATUS_NOT_FOUND         = 'NOT_FOUND';
    const STATUS_OK                = 'OK';

    protected $orderResourceModelFactory;
    protected $workflowServiceApi;
    protected $orderQueueCollectionFactory;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Magento\Sales\Model\ResourceModel\OrderFactory $orderResourceModelFactory,
        \Hotlink\Brightpearl\Helper\Api\Service\Workflow $workflowServiceApi,
        \Hotlink\Brightpearl\Model\ResourceModel\Queue\Order\CollectionFactory $orderQueueCollectionFactory
        )
    {
        $this->orderResourceModelFactory = $orderResourceModelFactory;
        $this->workflowServiceApi = $workflowServiceApi;
        $this->orderQueueCollectionFactory = $orderQueueCollectionFactory;

        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );
    }

    protected function _getName()
    {
        return 'Hotlink Brightpearl: Magento Sales Order Reconciliation';
    }

    function execute()
    {
        if ( $this->getEnvironment()->isOAuth2Active() )
            {
                $this->execute_oauth();
            }
        else
            {
                $this->execute_legacy();
            }
    }

    function execute_oauth()
    {
        $report = $this->getReport();
        $env = $this->getEnvironment();
        $report->__invoke($env, 'status');

        $batch  = $env->getParameter( 'batch' )->getValue();
        $sleep  = $env->getParameter( 'sleep' )->getValue();
        $ignore = $env->getParameter( 'ignore_past_minutes' )->getValue();
        $fallbackStartDate = $env->getParameter( 'fallback_start_date' )->getValue();
        $requeueErrors     = $env->getParameter( 'requeue_with_errors' )->getValue();
        $currentInstanceId = $env->getOAuth2InstanceId();
        $currentAuthToken  = $env->getAuthToken();

        if ( $fallbackStartDate ) {
            if ( \DateTime::createFromFormat('Y-m-d H:i:s', $fallbackStartDate ) ||
                 \DateTime::createFromFormat('Y-m-d', $fallbackStartDate ) ) {

                $fallbackStartDate = new \DateTime( $fallbackStartDate, new \DateTimeZone( 'UTC' ) );
            }
            else {
                $report->error( 'Invalid fallback start date value/format !' );
                $fallbackStartDate = null;
                return;
            }
        }
        else {
            $fallbackStartDate = null;
        }

        $collection = $this->initCollection($batch, $ignore, $fallbackStartDate);

        $pages = ( $collection->getSize() > 0 )
            ? $collection->getLastPageNumber()
            : 0;

        if ( $pages ) {

            $reconciled = 0;
            $requeue    = 0;
            $skipped    = 0;
            $currentPage = 0;

            while ( ++$currentPage <= $pages ) {

                $report->info("Processing batch $currentPage of $pages")->setBatch( $currentPage )->indent();

                $collection->clear()->load();


                // 1. Filter out queue items that have not been sent with $currentAuthToken
                $report->info( "Filtering out items with token mismatch" )->indent();
                $items = $collection->getItems();
                foreach ($items as $item) {
                    if ( $item->getSentOauthInstanceId() != $currentInstanceId ) {
                        $item->setReconciliationStatus( self::STATUS_INSTANCE_MISMATCH );
                        $item->setReconciliationOAuthInstanceId( $currentInstanceId );
                        $item->save();

                        if ($sleep) {
                            usleep($sleep);
                        }

                        $collection->removeItemByKey( $item->getId() );
                        $skipped++;
                    }
                }
                $report->debug( $skipped . ' skipped due to instance mismatch' )->unindent();

                if ( $collection->count() > 0 ) {

                    // 2. Ask BP

                    $ordersInfo  = $collection->walk( array($this, 'getQueueItemInfo') );
                    $apiStatuses = $this->apiGetOrdersStatus( $ordersInfo );

                    $notFound   = array_filter( $apiStatuses, 'is_null' );
                    $withErrors = array_filter( $apiStatuses, array($this, 'hasError') );
                    $withStatus = array_filter( $apiStatuses, array($this, 'hasStatus') );

                    $report->debug( sprintf( '%d not found, %d with errors, %d with status',
                                             count($notFound),
                                             count($withErrors),
                                             count($withStatus) ) );

                    // 3. update queue items

                    foreach ( $withErrors as $weId => $we ) {
                        if ( $item = $collection->getItemById( $weId ) ) {

                            $item->setReconciliationStatus( self::STATUS_WITH_ERROR );
                            $item->setReconciliationOauthInstanceId( $currentInstanceId );
                            if ( $requeueErrors ) {
                                $item->setSendToBp( 1 );
                                $requeue++;
                            }
                            $item->save();

                            if ($sleep) {
                                usleep($sleep);
                            }
                        }
                    }

                    foreach ( $notFound as $nfId => $nf) {
                        if ( $item = $collection->getItemById( $nfId ) ) {

                            $item->setReconciliationStatus( self::STATUS_NOT_FOUND );
                            $item->setReconciliationOauthInstanceId( $currentInstanceId );
                            $item->setSendToBp( 1 );
                            $item->setInBp( 0 );
                            $item->save();

                            if ($sleep) {
                                usleep($sleep);
                            }

                            $requeue++;
                        }
                    }

                    // Only stamp queue items that have orders in BP
                    foreach ($withStatus as $wsId => $ws) {
                        if ( $item = $collection->getItemById($wsId) ) {

                            $item->setReconciliationStatus( self::STATUS_OK );
                            $item->setReconciliationOauthInstanceId( $currentInstanceId );
                            $item->setInBp( 1 );
                            $item->setReconciledAt( gmdate( \Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT ) );
                            $item->save();

                            if ( $sleep ) {
                                usleep( $sleep );
                            }

                            $reconciled++;
                        }
                    }
                }
                else {
                    $report->debug( 'No items left for processing' );
                }

                $report->unindent();
            }

            $report->info( sprintf( '%d requeued, %d reconciled',
                                    $requeue,
                                    $reconciled ));


            $report->setSuccess( $reconciled );
        }
        else {
            $report->debug( "No queue items found that satisfy these filters." );
        }

    }

    function execute_legacy()
    {
        $report = $this->getReport();
        $env = $this->getEnvironment();
        $report->__invoke($env, 'status');

        $batch  = $env->getParameter( 'batch' )->getValue();
        $sleep  = $env->getParameter( 'sleep' )->getValue();
        $ignore = $env->getParameter( 'ignore_past_minutes' )->getValue();
        $fallbackStartDate = $env->getParameter( 'fallback_start_date' )->getValue();
        $requeueErrors     = $env->getParameter( 'requeue_with_errors' )->getValue();
        $currentAuthToken  = $env->getAuthToken();

        if ( $fallbackStartDate ) {
            if ( \DateTime::createFromFormat('Y-m-d H:i:s', $fallbackStartDate ) ||
                 \DateTime::createFromFormat('Y-m-d', $fallbackStartDate ) ) {

                $fallbackStartDate = new \DateTime( $fallbackStartDate, new \DateTimeZone( 'UTC' ) );
            }
            else {
                $report->error( 'Invalid fallback start date value/format !' );
                $fallbackStartDate = null;
                return;
            }
        }
        else {
            $fallbackStartDate = null;
        }

        $collection = $this->initCollection($batch, $ignore, $fallbackStartDate);

        $pages = ( $collection->getSize() > 0 )
            ? $collection->getLastPageNumber()
            : 0;

        if ( $pages ) {

            $reconciled = 0;
            $requeue    = 0;
            $skipped    = 0;
            $currentPage = 0;

            while ( ++$currentPage <= $pages ) {

                $report->info("Processing batch $currentPage of $pages")->setBatch( $currentPage )->indent();

                $collection->clear()->load();


                // 1. Filter out queue items that have not been sent with $currentAuthToken
                $report->info( "Filtering out items with token mismatch" )->indent();
                $items = $collection->getItems();
                foreach ($items as $item) {
                    if ( $item->getSentToken() != $currentAuthToken ) {
                        $item->setReconciliationStatus( self::STATUS_TOKEN_MISMATCH );
                        $item->setReconciliationToken( $currentAuthToken );
                        $item->save();

                        if ($sleep) {
                            usleep($sleep);
                        }

                        $collection->removeItemByKey( $item->getId() );
                        $skipped++;
                    }
                }
                $report->debug( $skipped . ' skipped due to token mismatch' )->unindent();

                if ( $collection->count() > 0 ) {

                    // 2. Ask BP

                    $ordersInfo  = $collection->walk( array($this, 'getQueueItemInfo') );
                    $apiStatuses = $this->apiGetOrdersStatus( $ordersInfo );

                    $notFound   = array_filter( $apiStatuses, 'is_null' );
                    $withErrors = array_filter( $apiStatuses, array($this, 'hasError') );
                    $withStatus = array_filter( $apiStatuses, array($this, 'hasStatus') );

                    $report->debug( sprintf( '%d not found, %d with errors, %d with status',
                                             count($notFound),
                                             count($withErrors),
                                             count($withStatus) ) );

                    // 3. update queue items

                    foreach ( $withErrors as $weId => $we ) {
                        if ( $item = $collection->getItemById( $weId ) ) {

                            $item->setReconciliationStatus( self::STATUS_WITH_ERROR );
                            $item->setReconciliationToken( $currentAuthToken );
                            if ( $requeueErrors ) {
                                $item->setSendToBp( 1 );
                                $requeue++;
                            }
                            $item->save();

                            if ($sleep) {
                                usleep($sleep);
                            }
                        }
                    }

                    foreach ( $notFound as $nfId => $nf) {
                        if ( $item = $collection->getItemById( $nfId ) ) {

                            $item->setReconciliationStatus( self::STATUS_NOT_FOUND );
                            $item->setReconciliationToken( $currentAuthToken );
                            $item->setSendToBp( 1 );
                            $item->setInBp( 0 );
                            $item->save();

                            if ($sleep) {
                                usleep($sleep);
                            }

                            $requeue++;
                        }
                    }

                    // Only stamp queue items that have orders in BP
                    foreach ($withStatus as $wsId => $ws) {
                        if ( $item = $collection->getItemById($wsId) ) {

                            $item->setReconciliationStatus( self::STATUS_OK );
                            $item->setReconciliationToken( $currentAuthToken );
                            $item->setInBp( 1 );
                            $item->setReconciledAt( gmdate( \Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT ) );
                            $item->save();

                            if ( $sleep ) {
                                usleep( $sleep );
                            }

                            $reconciled++;
                        }
                    }
                }
                else {
                    $report->debug( 'No items left for processing' );
                }

                $report->unindent();
            }

            $report->info( sprintf( '%d requeued, %d reconciled',
                                    $requeue,
                                    $reconciled ));


            $report->setSuccess( $reconciled );
        }
        else {
            $report->debug( "No queue items found that satisfy these filters." );
        }
    }

    protected function apiGetOrdersStatus( array $info )
    {
        $report = $this->getReport();

        $report->info( 'Requesting order information' )->indent();;

        $statuses = array();
        foreach ( $info as $id => $item ) {
            list( $incrementId, $storeId ) = $item;
            $report->addReference( $incrementId );

            $statuses[ $id ] = $report->__invoke( $this->workflowServiceApi,
                                                  'getOrderStatus',
                                                  $storeId,
                                                  $this->getEnvironment()->getAccountCode(),
                                                  $incrementId );
        }

        $report->unindent();

        return $statuses;
    }

    function getQueueItemInfo( \Hotlink\Brightpearl\Model\Queue\Order $item )
    {
        return array( $item->getIncrementId(), $item->getStoreId() );
    }

    protected function hasError( $item )
    {
        return ($item && $item->getError() );
    }

    protected function hasStatus( $item )
    {
        return ($item && $item->getStatus() );
    }

    function extractItemFields( array $fields, $collection, $indexField = null )
    {
        $result = array();

        foreach ($collection as $item) {

            $data = array();
            foreach ($fields as $field) {
                $data[] = $item->getData( $field );
            }

            $indexField
                ? $result[ $item->getData( $indexField ) ] = $data
                : $result[] = $data;
        }

        return $result;
    }

    protected function initCollection($batch, $ignore, \DateTime $startDate = null)
    {
        $report = $this->getReport();


        $startMessage = ( $startDate ? ' and sent_at >= '. $startDate->format( 'Y-m-d H:i:s' ) . ' UTC' : '.' );
        $report->info( 'Loading order queue items in batches of '.$batch.
                       ' where: sent_at not in the past '.$ignore.' minutes'.$startMessage );


        $collection = $this->orderQueueCollectionFactory->create();
        $orderResource = $this->orderResourceModelFactory->create();

        $collection->getSelect()->join(
            array( 'order'=> $orderResource->getMainTable() ),  // table
            'order.entity_id=main_table.order_id',                            // ON
            array( 'increment_id', 'store_id' )                               // columns
            );

        $collection->addFieldToFilter( 'send_to_bp', array( 'eq' => 0) );                   // ignore already re-queued
        $collection->addFieldToFilter( 'main_table.reconciled_at', array('null' => true) ); // ignore successfully reconciled
        $collection->addFieldToFilter( 'sent_at', array( 'notnull' => true) );              // ignore not sent yet
        $collection->getSelect()->where(
            new \Zend_Db_Expr( 'sent_at < NOW() - INTERVAL '.$ignore.' MINUTE') );           // ignore recently sent

        $collection->addFieldToFilter(
            'reconciliation_status',
            array(
                array( // OR
                    array( 'null' => true),
                    array( 'nin'  => array( self::STATUS_OK, self::STATUS_TOKEN_MISMATCH, self::STATUS_INSTANCE_MISMATCH ) )
                    )
                )
            );

        if ( !is_null($startDate) ) {
            $mysqlFormatStartDate = $startDate->format( 'Y-m-d H:i:s' );
            $collection->addFieldToFilter( 'sent_at', array( 'gteq' => $mysqlFormatStartDate) );
        }

        $collection->setPageSize($batch);

        return $collection;
    }
}
