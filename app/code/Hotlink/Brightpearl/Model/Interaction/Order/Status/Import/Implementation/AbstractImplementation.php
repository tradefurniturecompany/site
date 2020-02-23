<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Import\Implementation;

abstract class AbstractImplementation extends \Hotlink\Brightpearl\Model\Interaction\Order\Implementation\AbstractImplementation
{
    protected $orderStatusCollectionFactory;
    protected $orderFactory;
    protected $orderSearchApi;
    protected $period;
    protected $workflowServiceApi;
    protected $orderServiceApi;
    protected $idset;
    protected $orderService;
    protected $orderCollectionFactory;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $orderStatusCollectionFactory,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Hotlink\Brightpearl\Helper\Api\Service\Search\Order $orderSearchApi,
        \Hotlink\Brightpearl\Helper\Api\Service\Search\Period $period,
        \Hotlink\Brightpearl\Helper\Api\Service\Workflow $workflowServiceApi,
        \Hotlink\Brightpearl\Helper\Api\Service\Order $orderServiceApi,
        \Hotlink\Brightpearl\Helper\Api\Idset $idset
        )
    {
        $this->orderStatusCollectionFactory = $orderStatusCollectionFactory;
        $this->orderFactory = $orderFactory;
        $this->orderService = $orderService;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderSearchApi = $orderSearchApi;
        $this->period = $period;
        $this->workflowServiceApi = $workflowServiceApi;
        $this->orderServiceApi = $orderServiceApi;
        $this->idset = $idset;

        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );
    }

    protected function mapOrderState( $state, $status, $notifyCustomer, \Magento\Sales\Model\Order $order )
    {
        $this->getReport()->info( "Setting order state to [$state] and status to [$status]" )->indent();

        // setState has moved to the OrderService
        //$order->setState( $state, $status, 'Set by Hotlink Brightpearl on order status import interaction', $notifyCustomer);
        $this->orderService->setState( $order,
                                       $state,
                                       $status,
                                       'Set by Hotlink Brightpearl on order status import interaction',
                                       $notifyCustomer,
                                       // This the $shouldProtectState arg - even though common sense
                                       // would be to have it TRUE (which will allow magento to protect states)
                                       // unfortunately the service uses the order missing function 'isStateProtected',
                                       // which causes a Fatal Error. go figure !
                                       false );

        $this->getReport()->unindent();

        return $order;
    }

    protected function saveOrder( $order, $sleep = null )
    {
        $order->save();

        $this->reportOrderInfo( $order, 'Order successfully saved' );
        $this->getReport()->incSuccess();

        if ( $sleep ) {
            usleep($sleep);
        }

        return $order;
    }

    protected function getAssignedState( $status )
    {
        $item = $this->orderStatusCollectionFactory->create()
            ->joinStates()
            ->addFieldToFilter( 'main_table.status', $status )
            ->getFirstItem();

        return $item ? $item->getState() : null;
    }

    protected function getOrderStatusMapReverseLookup( $bpStatus )
    {
        $env = $this->getEnvironment();
        $map = $env->getConfig()->getOrderStatusMap( $env->getStoreId() );

        $mageStatus = null;
        if ( $map ) {

            foreach ($map as $row) {
                $mapBPStatus   = isset( $row['brightpearl']) ? $row['brightpearl'] : null;
                $mapMageStatus = isset( $row['magento'])     ? $row['magento']     : null;

                if ( $bpStatus == $mapBPStatus ) {
                    $mageStatus = $mapMageStatus;
                    break;
                }
            }
        }

        return $mageStatus;
    }

    protected function apiSearchOrders( $lookbehindDate, $batch, $firstResult )
    {
        $report  = $this->getReport();
        $env     = $this->getEnvironment();
        $storeId = $env->getStoreId();

        $date = new \DateTime( $lookbehindDate, new \DateTimeZone( 'UTC' ) );
        $updatedOn = $this->period->toAfter( $date );

        $report->info( 'Searching for orders where updatedOn=' . $updatedOn )->indent();
        $report->debug("pageSize=$batch, firstResult=$firstResult");

        $filters = [ 'updatedOn' => $updatedOn ];

        $result = $report( $this->orderSearchApi,
                           'search',
                           $storeId,
                           $env->getAccountCode(),
                           $filters,
                           $batch,
                           $firstResult,
                           $env->getConfig()->getSortBy( $storeId ),
                           $env->getConfig()->getSortDirection( $storeId ) );

        $nrResults = $result->getPagination()->getData('resultsReturned');

        $report->debug('Results returned = '.$nrResults)->unindent();

        return $result;
    }

    protected function workflowApiGetOrder( \Magento\Sales\Model\Order $order )
    {
        $report = $this->getReport();
        $env    = $this->getEnvironment();

        $report->info( 'Requesting order details' )->indent();

        $order = $report( $this->workflowServiceApi,
                          'getOrderStatus',
                          $env->getStoreId(),
                          $env->getAccountCode(),
                          $order->getIncrementId() );
        if ( !$order ) {
            $report->error( 'API did not return order info' );
        }

        $report->unindent();

        return $order;
    }

    protected function serviceApiGetOrder( array $bpOrderIds )
    {
        $report = $this->getReport();
        $env    = $this->getEnvironment();

        $report->info("Requesting order info")->indent();

        $idSet = $this->idset->unorderedList( $bpOrderIds );

        $result = $report( $this->orderServiceApi,
                           'getOrders',
                           $env->getStoreId(),
                           $env->getAccountCode(),
                           $idSet );

        $report->unindent();

        return $result;
    }

    protected function isStateMutable( \Magento\Sales\Model\Order $order )
    {
        return ! in_array( $order->getState(),
                           [ \Magento\Sales\Model\Order::STATE_COMPLETE,
                             \Magento\Sales\Model\Order::STATE_CLOSED,
                             \Magento\Sales\Model\Order::STATE_CANCELED ] );
    }

    protected function reportOrderInfo( \Magento\Sales\Model\Order $order, $info = null )
    {
        $report = $this->getReport();

        if ($info) {
            $report->info( $info )->indent();
        }

        $state  = $order->getState();
        $status = $order->getStatus();

        $report->debug('state='. $state.', status='. $status);

        if ($info) {
            $report->unindent();
        }
    }

    protected function getOrCreateEnvironment( $storeId )
    {
        return $this->hasEnvironment( $storeId )
            ? $this->getEnvironment( $storeId )
            : $this->createEnvironment( $storeId );
    }

    protected function getMagentoOrder( $incrementId )
    {
        $order = $this->orderFactory->create()->loadByIncrementId( $incrementId );
        if ( is_null( $order->getId() ) ) {
            $order = null;
        }

        return $order;
    }

    protected function initCollection( $batch, array $incrementIdsFilter )
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToFilter( 'increment_id', [ 'in' => $incrementIdsFilter ]);
        $collection->setPageSize($batch);

        return $collection;
    }

    protected function platformDataColumn( $input, $columnKey, $indexColumn = null )
    {
        $result = array();
        foreach ($input as $data) {
            $value = $this->_dataColumn( $data, $columnKey );

            if ( $indexColumn ) {
                $result[ $this->_dataColumn( $data, $indexColumn ) ] = $value;
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }

    protected function _dataColumn( $data, $key )
    {
        if ( is_array( $key ) ) {
            $current = array_shift($key);
            if (!$key) {
                return $this->_dataColumn( $data, $current );
            }

            return $this->_dataColumn( $data->getData( $current ), $key ); // rec
        }

        return $data->getData( $key );
    }

}
