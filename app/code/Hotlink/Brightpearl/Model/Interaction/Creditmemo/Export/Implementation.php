<?php
namespace Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export;

class Implementation extends \Hotlink\Framework\Model\Interaction\Implementation\AbstractImplementation
{

    protected $date;

    protected $storeManager;
    protected $orderFactory;
    protected $queueCreditmemofactory;

    protected $apiIdsetHelper;
    protected $apiServiceOrderHelper;
    protected $apiServiceProductHelper;
    protected $apiServiceOrderSearch;
    protected $apiServiceAccounting;
    protected $apiServiceWorkflow;
    protected $apiServiceWarehouse;
    protected $apiServiceIntegration;

    protected $lookupWarehouseCollectionFactory;
    protected $lookupPricelistCollectionFactory;
    
    protected $dataCreditmemoExportFactory;
    protected $dataRefundExportFactory;
    protected $dataQuarantineExportFactory;

    protected $_warehouses;
    protected $_pricelists;

    protected $_queueCache = [];
    protected $_orderCache = [];

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Magento\Framework\Stdlib\DateTime\DateTime $date,

        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Hotlink\Brightpearl\Model\Queue\CreditmemoFactory $queueCreditmemofactory,
        \Hotlink\Brightpearl\Helper\Api\Idset $apiIdsetHelper,
        \Hotlink\Brightpearl\Helper\Api\Service\Order $apiServiceOrderHelper,
        \Hotlink\Brightpearl\Helper\Api\Service\Product $apiServiceProductHelper,
        \Hotlink\Brightpearl\Helper\Api\Service\Search\Order $apiServiceOrderSearch,
        \Hotlink\Brightpearl\Helper\Api\Service\Accounting $apiServiceAccounting,
        \Hotlink\Brightpearl\Helper\Api\Service\Workflow $apiServiceWorkflow,
        \Hotlink\Brightpearl\Helper\Api\Service\Warehouse $apiServiceWarehouse,
        \Hotlink\Brightpearl\Helper\Api\Service\Integration $apiServiceIntegration,
        
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Warehouse\CollectionFactory $lookupWarehouseCollectionFactory,
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Price\ListPrice\Item\CollectionFactory $lookupPricelistCollectionFactory,

        \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Creditmemo\ExportFactory $dataCreditmemoExportFactory,
        \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Refund\ExportFactory $dataRefundExportFactory,
        \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Quarantine\ExportFactory $dataQuarantineExportFactory

    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );

        $this->date = $date;

        $this->queueCreditmemofactory = $queueCreditmemofactory;
        $this->storeManager = $storeManager;
        $this->orderFactory = $orderFactory;

        $this->apiIdsetHelper = $apiIdsetHelper;
        $this->apiServiceOrderHelper = $apiServiceOrderHelper;
        $this->apiServiceProductHelper = $apiServiceProductHelper;
        $this->apiServiceOrderSearch = $apiServiceOrderSearch;
        $this->apiServiceAccounting = $apiServiceAccounting;
        $this->apiServiceWorkflow = $apiServiceWorkflow;
        $this->apiServiceWarehouse = $apiServiceWarehouse;
        $this->apiServiceIntegration = $apiServiceIntegration;
    
        $this->lookupWarehouseCollectionFactory = $lookupWarehouseCollectionFactory;
        $this->lookupPricelistCollectionFactory = $lookupPricelistCollectionFactory;

        $this->dataQuarantineExportFactory = $dataQuarantineExportFactory;
        $this->dataCreditmemoExportFactory = $dataCreditmemoExportFactory;
        $this->dataRefundExportFactory = $dataRefundExportFactory;
    }


    protected function _getName()
    {
        return 'Hotlink Brightpearl: Magento Creditmemo Exporter';
    }

    function execute()
    {
        $report = $this->getReport();
        $environment = $this->getEnvironment();
        $report( $environment, 'status' );

        // force parameter only exists when interaction is triggered manually (from admin screen)
        $forceSalesCredit = ( $forcedParam = $environment->getParameter( 'force-salescredit' ) )
                          ? $forcedParam->getValue()
                          : false;

        $forceRefund = ( $forcedParam = $environment->getParameter( 'force-refund' ) )
                     ? $forcedParam->getValue()
                     : false;

        $forceQuarantine = ( $forcedParam = $environment->getParameter( 'force-quarantine' ) )
                         ? $forcedParam->getValue()
                         : false;

        $stream = $environment->getParameter('stream');
        $creditmemos = $stream->getValue();
        $count = 0;

        foreach ( $creditmemos as $creditmemo )
            {
                $incrementId = $creditmemo->getIncrementId();
                $storeId = $creditmemo->getStoreId();
                $count++;

                if ( is_null( $storeId ) )
                    {
                        if ( $forceSalesCredit || $forceRefund || $forceQuarantine )
                            {
                                // creditmemo is missing store_id. use admin if order export is forced.
                                // @see else case, read the error message.

                                $storeId = $this->storeManager->getStore( \Magento\Store\Model\Store::ADMIN_CODE )->getId();
                                $report->debug( "Creditmemo [$incrementId] is missing store id. Since export is forced admin store is assumed." );
                            }
                        else
                            {
                                $report
                                    ->incFail()
                                    ->error( "Creditmemo [$incrementId] is missing store id. This can happen when the store this order was placed from does not exist anymore. In order to export this order please force export it manually." );
                                continue;
                            }
                    }

                $environment = $this->getOrCreateEnvironment( $storeId );

                if ( $environment->isEnabled() )
                    {
                        $order = $this->getOrder( $creditmemo->getOrderId() );
                        $orderIncrementId = $order->getIncrementId();
                        $report->addReference( $incrementId );
                        $report->addReference( $orderIncrementId );
                        $report
                            ->info( "Processing creditmemo $incrementId for order $orderIncrementId" )
                            ->indent();

                        $queue = $this->getQueueItem( $creditmemo );

                        // ignore tracking flags when export is forced
                        $send = ( $forceSalesCredit || $forceRefund || $forceQuarantine ) ? true : $queue->shouldSend();
                        if ( $send )
                            {
                                if ( $this->transmit( $creditmemo, $environment, $queue, $forceSalesCredit, $forceRefund, $forceQuarantine ) )
                                    {
                                        $report->incSuccess()->info( "Creditmemo export successful" );
                                    }
                                else
                                    {
                                        $report->incFail()->info( "Creditmemo export failed" );
                                    }
                            }
                        else
                            {
                                $report->info( "Creditmemo skipped" );
                            }
                    }
                else
                    {
                        $report->debug( "Interaction disabled in store $storeId" );
                    }
                $report->unindent();
            }

        if ( 0 === $count )
            {
                $report->debug( 'No creditmemos to process' );
            }

        return $this;
    }

    protected function transmit( $creditmemo, $environment, $queue, $forceSalesCredit, $forceRefund, $forceQuarantine )
    {
        $success = false;
        $report = $this->getReport();
        $restoreIndentation = $report->getIndent();
        //
        // Send to BP API
        //
        try
            {
                $report( $queue, 'status', 'Using Creditmemo tracking information' );
                $attempted = false;
                $report->indent();
                if ( $forceSalesCredit || ( ! $queue->getSalesCreditInBp() ) )  // create sales credit
                    {
                        $attempted = true;

                        $report->info( "Creating Sales Credit in Brightpearl" );
                        $report->indent();

                        $result = $this->createSalesCreditExn( $creditmemo, $environment );

                        $bpSalesCreditId = $result[ 'salesCreditId' ];
                        $bpOrderId = $result[ 'bpOrderId'];

                        $queue->setSalesCreditId( $bpSalesCreditId );
                        $queue->setSalesCreditSentAt( $this->date->gmtDate() );
                        $queue->setBpOrderId( $bpOrderId );
                        $queue->setSalesCreditInBp( true );
                        $queue->save();

                        $report->unindent();
                        $report->info( "Created Sales Credit $bpSalesCreditId" );
                    }
                if ( ! is_null( $queue->getRefundInBp() ) )                 // refund is required
                    {
                        if ( $forceRefund || ( ! $queue->getRefundInBp() ) )      // create refund
                            {
                                $attempted = true;

                                $report->info( "Creating Refund in Brightpearl" );
                                $report->indent();

                                $refundId = $this->createRefundExn( $creditmemo, $environment, $queue->getSalesCreditId() );
                                $queue->setRefundId( $refundId );
                                $queue->setRefundSentAt( $this->date->gmtDate() );
                                $queue->setRefundInBp( true );
                                $queue->save();

                                $report->unindent();
                                $report->info( "Created Refund $refundId" );
                            }
                    }
                if ( ! is_null( $queue->getQuarantineInBp() ) )             // quarantine is required
                    {
                        if ( $forceQuarantine || ( ! $queue->getQuarantineInBp() ) ) // create quarantine
                            {
                                $attempted = true;

                                $report->debug( "Creating Quarantine Note in Brightpearl" );
                                $report->indent();

                                $quarantineId = $this->createQuarantineExn( $creditmemo, $environment, $queue->getSalesCreditId() );
                                $queue->setQuarantineId( $quarantineId );
                                $queue->setQuarantineSentAt( $this->date->gmtDate() );
                                $queue->setQuarantineInBp( true );
                                $queue->save();

                                $report->unindent();
                                $report->info( "Created Quarantine Note $quarantineId" );
                            }
                    }
                if ( $attempted )
                    {
                        $queue->setSentToken( $environment->getAuthToken() );
                        $queue->setSendToBp( false );
                        $queue->setSentAt( $this->date->gmtDate() );
                        $success = true;
                    }
            }
        catch ( \Exception $e )
            {
                $report->unindent();
                $report->error( "Unable to export creditmemo", $e );
                if ( ! ( $forceSalesCredit || $forceRefund || $forceQuarantine ) )
                    {
                        $queue->setSendToBp( true );
                    }
            }
        //
        // Update tracking
        //
        $queue->save();
        $report->setIndent( $restoreIndentation );
        $report( $queue, 'status', 'Tracking information updated successfully' );
        return $success;
    }

    //
    //  Create Quarantine Note
    //
    protected function createQuarantineExn( $creditmemo, $environment, $salesCreditId )
    {
        $report = $this->getReport();
        $refund = false;

        //
        //  Confirm warehouse configured
        //
        $warehouse = $this->getWarehouse( $environment->getQuarantineWarehouse() );

        //
        //  Confirm pricelist configured
        //
        $pricelist = $this->getPricelist( $environment->getQuarantinePricelist() );

        //
        //  Confirm price list currency matches credit note currency
        //
        if ( $pricelist->getCurrencyCode() != $environment->getCurrencyCode( $creditmemo ) )
            {
                $pricelistId = $pricelist->getId();
                $message = "Currency code mismatch."
                         . "Coinfigured pricelist (" . $pricelist->getName() . " [$pricelistId])"
                         . " has currency code " . $pricelist->getCurrencyCode()
                         . " but the creditmemo is using currency code " . $environment->getCurrencyCode( $creditmemo );
                $this->exceptionHelper->throwConfiguration( $message, $this );
            }

        //
        //  Retrieve Sales Credit
        //
        $bpSalesCredit = $this->apiGetSalesCredit( $environment, $salesCreditId );
        if ( $rows = $bpSalesCredit->getRows() )
            {
                $productIds = array_keys( $this->index( $rows, 'productId' ) );         // Removes non-unique duplicates
                $skus = array_keys( $this->index( $creditmemo->getItems(), 'sku' ) );   // Removes non-unique duplicates

                $bpProducts = $this->apiGetProducts( $environment, $productIds );
                $bpPriceList = $this->apiGetPriceList( $environment, $pricelist->getBrightpearlId(), $skus );

                $data = $this->mapQuarantineNote( $environment,
                                                  $creditmemo,
                                                  $bpSalesCredit,
                                                  $bpProducts,
                                                  $bpPriceList,
                                                  $warehouse->getBrightpearlId(),
                                                  $warehouse );

                $quarantineNote = $this->apiCreateQuarantineNote( $environment, $salesCreditId, $data );
                return $quarantineNote->getGoodsinNoteId();
            }
    }

    protected function apiCreateQuarantineNote( $environment, $bpPurchaseOrderId, $quarantineGoodsinNote )
    {
        $report = $this->getReport();
        return $report( $this->apiServiceWarehouse,
                        'exportGoodsinNote',
                        $environment->getStoreId(),
                        $environment->getAccountCode(),
                        $bpPurchaseOrderId,
                        $quarantineGoodsinNote );
    }

    protected function mapQuarantineNote( $environment, $creditmemo, $bpSalesCredit, $bpProducts, $bpPriceList, $bpWarehouseId, $warehouse )
    {
        $report = $this->getReport();
        $data = $this->dataQuarantineExportFactory->create();
        $data->setHelper( $environment );

        $extra = [ 'bpSalesCredit' => $bpSalesCredit,
                   'bpProducts'    => $bpProducts,
                   'bpPriceList'   => $bpPriceList,
                   'bpWarehouseId' => $bpWarehouseId,
                   'warehouse'     => $warehouse ];

        $report( $data, 'map', $creditmemo, \Hotlink\Brightpearl\Model\Platform\Type::MAGEMODEL, $extra );

        return $data;
    }

    protected function apiGetSalesCredit( $environment, $salesCreditId )
    {
        $report = $this->getReport();
        $salesCredits = $report( $this->apiServiceOrderHelper,
                                 'getSalesCredits',
                                 $environment->getStoreId(),
                                 $environment->getAccountCode(),
                                 $salesCreditId );
        if ( count( $salesCredits ) == 0 )
            {
                $this->exceptionHelper->throwProcessing( "No Sales Credit Notes returned by api for id [$salesCreditId]", $this );
            }
        if ( count( $salesCredits ) != 1 )
            {
                $this->exceptionHelper->throwProcessing( "Multiple Sales Credit Notes returned by api when expecting only 1 result for id [$salesCreditId]", $this );
            }
        return $salesCredits[ 0 ];
    }

    protected function index( $items, $field = 'id' )
    {
        $indexed = [];
        foreach ( $items as $item )
            {
                $itemId = $item->getData( $field );
                $indexed[ $itemId ] = $item;
            }
        return $indexed;
    }

    protected function apiGetProducts( $environment, $productIds )
    {
        $report = $this->getReport();
        $idSet = $this->apiIdsetHelper->unorderedList( $productIds );
        return $report( $this->apiServiceProductHelper,
                        'getProducts',
                        $environment->getStoreId(),
                        $environment->getAccountCode(),
                        $idSet );
    }

    protected function apiGetPriceList( $environment, $priceListId, $skus )
    {
        $report = $this->getReport();
        $priceListPrices = $report( $this->apiServiceWorkflow,
                                    'getProductPricing',
                                    $environment->getStoreId(),
                                    $environment->getAccountCode(),
                                    [ $priceListId ],
                                    $skus );
        if ( count( $priceListPrices ) == 0 )
            {
                $this->exceptionHelper->throwProcessing( "No Price Lists returned by api for pricing of list [$priceListId]", $this );
            }
        if ( count( $priceListPrices ) != 1 )
            {
                $this->exceptionHelper->throwProcessing( "Multiple Price Lists returned by api when expecting only 1 for pricing of list [$priceListId]", $this );
            }
        return $priceListPrices[ 0 ];
    }

    //
    //  Create Refund
    //
    protected function createRefundExn( $creditmemo, $environment, $bpOrderId )
    {
        $report = $this->getReport();
        $refund = false;

        $order = $this->getOrder( $creditmemo->getOrderId() );

        $payment = $order->getPayment();
        $nominalCode = $environment->getPaymentNominalCode( $payment );

        $data = $this->mapRefund( $environment, $creditmemo, $bpOrderId, $nominalCode );

        $refund = $this->apiCreateRefund( $environment, $data );

        return $refund->getRefundId();
    }

    protected function mapRefund( $environment, $creditmemo, $bpOrderId, $nominalCode )
    {
        $report = $this->getReport();
        $data = $this->dataRefundExportFactory->create();
        $data->setHelper( $environment );

        $extra = [ "brightpearlOrderId" => $bpOrderId,
                   "paymentMethodCode"  => $nominalCode ];
        $report( $data, 'map', $creditmemo, \Hotlink\Brightpearl\Model\Platform\Type::MAGEMODEL, $extra );

        return $data;
    }

    protected function apiCreateRefund( $environment, $data )
    {
        $report = $this->getReport();
        $storeId = $environment->getStoreId();
        $accountCode = $environment->getAccountCode();
        return $report( $this->apiServiceAccounting, 'exportRefund', $storeId, $accountCode, $data );
    }

    //
    //  Create Sales Credit
    //
    protected function createSalesCreditExn( $creditmemo, $environment )
    {
        $report = $this->getReport();

        $result = false;
        $salesCredit = false;
        $bpOrderId = false;
        $bpSalesCreditId = false;
        //
        //  Find the order in Magento
        //
        if ( $order = $this->getOrder( $creditmemo->getOrderId() ) )
            {
                $incrementId = $order->getIncrementId();

                //
                //  Obtain installedIntegrationInstanceId
                //
                if ( $integrationInstanceId = $this->apiGetIntegrationInstanceId( $environment ) )
                    {
                        //
                        //  Find order in Brightpearl
                        //
                        if ( $brightpearlFound = $this->apiFindOrder( $environment, $integrationInstanceId, $incrementId ) )
                            {
                                $bpOrderId = $brightpearlFound[ 'orderId' ];
                                //
                                //  Get the found order (need row item productId's)
                                //
                                if ( $brightpearlOrder = $this->apiGetOrder( $environment, $bpOrderId ) )
                                    {
                                        //
                                        //  Export sales credit data
                                        //
                                        $data = $this->mapCreditmemo( $environment, $creditmemo, $brightpearlOrder );
                                        
                                        if ( $environment->getQuarantineEnabled() )
                                            {
                                                $warehouse = $this->getWarehouse( $environment->getQuarantineWarehouse() );
                                                $data[ "warehouseId" ] = $warehouse->getBrightpearlId();

                                                $pricelist = $this->getPricelist( $environment->getQuarantinePricelist() );
                                                $data[ "priceListId" ] = $pricelist->getBrightpearlId();
                                            }
                                        
                                        $salesCredit = $this->apiCreateSalesCredit( $environment, $data );
                                        $bpSalesCreditId = $salesCredit->getSalesCreditId();
                                    }
                                else
                                    {
                                        $message = "Failed to GET Brightpearl order [$bpOrderId] from api";
                                        $this->exceptionHelper->throwProcessing( $message, $this );
                                    }
                            }
                        else
                            {
                                $message = "Order [" . $order->getIncrementId() . "] not found in Brightpearl";
                                $this->exceptionHelper->throwProcessing( $message, $this );
                            }
                    }
                else
                    {
                        $message = "Unable to get installedIntegrationInstanceId from api";
                        $this->exceptionHelper->throwProcessing( $message, $this );
                    }
            }
        else
            {
                $message = "Order [" . $creditmemo->getOrderId() . "] for Creditmemo [" . $creditmemo->getIncrementId() . "] not found in Magento";
                $this->exceptionHelper->throwValidation( $message, $this );
            }
        $result[ 'bpOrderId'     ] = $bpOrderId;
        $result[ 'salesCreditId' ] = $bpSalesCreditId;

        return $result;
    }

    protected function apiGetIntegrationInstanceId( $environment )
    {
        $report = $this->getReport();
        $instance = $report( $this->apiServiceIntegration,
                             'getInstance',
                             $environment->getStoreId(),
                             $environment->getAccountCode() );
        if ( isset( $instance[ 'installedIntegrationInstanceId' ] ) )
            {
                return $instance[ 'installedIntegrationInstanceId' ];
            }
        return null;
    }

    protected function apiCreateSalesCredit( $environment, $data )
    {
        $report = $this->getReport();
        return $report( $this->apiServiceOrderHelper,
                        'exportCredit',
                        $environment->getStoreId(),
                        $environment->getAccountCode(),
                        $data );
    }

    protected function apiFindOrder( $environment, $integrationInstanceId, $incrementId )
    {
        $report = $this->getReport();
        $storeId = $environment->getStoreId();
        $accountCode = $environment->getAccountCode();
        $filters = [ 'externalRefs' => $incrementId, 'installedIntegrationInstanceId' => $integrationInstanceId ];
        $batch = 10;
        $firstResult = true;
        $sortBy = null;
        $sortDirection = null;
        $columns = [ 'contactId', 'orderId', 'createdOn', 'createdById', 'customerRef', 'externalRef', 'installedIntegrationInstanceId' ];
        $result =
                $report(
                    $this->apiServiceOrderSearch,
                    'search',
                    $storeId,
                    $accountCode,
                    $filters,
                    $batch,
                    $firstResult,
                    $sortBy,
                    $sortDirection,
                    $columns );
        if ( $result )
            {
                if ( $records = $result->getResults() )
                    {
                        if ( count ( $records ) == 1 )
                            {
                                return $records[ 0 ];
                            }
                    }
            }
        return false;
    }

    protected function apiGetOrder( $environment, $brightpearlOrderId )
    {
        $report = $this->getReport();
        $loaded = $report( $this->apiServiceOrderHelper,
                           'getOrders',
                           $environment->getStoreId(),
                           $environment->getAccountCode(),
                           $brightpearlOrderId );
        if ( $loaded )
            {
                if ( count( $loaded ) == 1 )
                    {
                        return array_pop( $loaded );
                    }
            }
        return false;
    }

    protected function mapCreditmemo( $environment, $creditmemo, $brightpearlOrder )
    {
        $report = $this->getReport();
        $data = $this->dataCreditmemoExportFactory->create();
        $data->setHelper( $environment );

        $report( $data, 'map', $creditmemo, \Hotlink\Brightpearl\Model\Platform\Type::MAGEMODEL, $brightpearlOrder );

        return $data;
    }

    //
    //  Helpers
    //
    protected function getOrCreateEnvironment($storeId)
    {
        return $this->hasEnvironment($storeId)
            ? $this->getEnvironment($storeId)
            : $this->createEnvironment($storeId);
    }

    //
    //  Magento Lookups
    //
    protected function getWarehouse( $id )
    {
        if ( ! $this->_warehouses )
            {
                $this->_warehouses = $this->lookupWarehouseCollectionFactory->create()->load();
            }
        $result = $this->_warehouses->getItemById( $id );
        if ( ! $result )
            {
                $this->exceptionHelper->throwConfiguration( "No warehouse with id [$id] exists. Have you imported Brightpearl settings and reconfigured as necessary?", $this );
            }
        return $result;
    }

    protected function getPricelist( $id )
    {
        if ( ! $this->_pricelists )
            {
                $this->_pricelists = $this->lookupPricelistCollectionFactory->create()->load();
            }
        $result = $this->_pricelists->getItemById( $id );
        if ( ! $result )
            {
                $this->exceptionHelper->throwConfiguration( "No pricelist with id [$id] exists. Have you imported Brightpearl settings and reconfigured as necessary?", $this );
            }
        return $result;
    }

    protected function getOrder( $id )
    {
        $order = false;
        if ( isset( $this->_orderCache[ $id ] ) )
            {
                $order = $this->_orderCache[ $id ];
            }
        else
            {
                $found = $this->orderFactory->create()->load( $id );
                if ( $found->getId() )
                    {
                        $order = $found;
                        $this->_orderCache[ $id ] = $order;
                    }
            }
        return $order;
    }

    protected function getQueueItem( $creditmemo )
    {
        $item = false;
        if ( $id = $creditmemo->getId() )
            {
                if ( isset( $this->_queueCache[ $id ] ) )
                    {
                        $item = $this->_queueCache[ $id ];
                    }
                else
                    {
                        $item = $this->queueCreditmemofactory->create()->load( $id, 'creditmemo_id' );
                        if ( ! $item->getId() )
                            {
                                $item = $this->initQueue( $item, $creditmemo );
                            }
                        $this->_queueCache[ $id ] = $item;
                    }
            }
        return $item;
    }

    protected function initQueue( $item, $creditmemo )
    {
        $config = $this->getInteraction()->getConfig();
        $storeId = $creditmemo->getStoreId();
        $item->setCreditmemoId( $creditmemo->getId() );
        $item->setSendToBp( true );
        $item->setSalesCreditInBp( false );
        $item->setRefundInBp( $config->getRefundsEnabled( $storeId ) ? false : null );
        $item->setQuarantineInBp( $config->getQuarantineEnabled( $storeId ) ? false : null );
        $item->save();
        return $item;
    }

}
