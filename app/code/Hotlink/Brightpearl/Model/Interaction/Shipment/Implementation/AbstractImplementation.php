<?php
namespace Hotlink\Brightpearl\Model\Interaction\Shipment\Implementation;

abstract class AbstractImplementation extends \Hotlink\Framework\Model\Interaction\Implementation\AbstractImplementation
{

    const MISSING_CARRIER = 'custom';
    const MISSING_NUMBER = '(none)';

    protected $apiServiceSearchPeriod;
    protected $apiServiceSearchOrder;
    protected $apiServiceSearchWarehouseGoodsoutnote;
    protected $apiIdset;
    protected $apiServiceWarehouse;
    protected $apiServiceOrder;
    protected $orderService;
    protected $shipmentFactory;
    protected $transactionFactory;
    protected $lookupShippingMethodFactory;
    protected $shipmentTrackingFactory;
    protected $orderCollectionFactory;

    protected $dataObjectFactory;
    protected $shipmentEmailSender;

    protected $shippingHelper;
    protected $moduleHelper;

    protected $warehouseCollectionFactory;
    protected $warehouses;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Magento\Sales\Model\Service\OrderService $orderService,  // TODO: lonesome

        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,

        \Hotlink\Brightpearl\Helper\Api\Service\Search\Period $apiServiceSearchPeriod,
        \Hotlink\Brightpearl\Helper\Api\Service\Search\Order $apiServiceSearchOrder,
        \Hotlink\Brightpearl\Helper\Api\Service\Search\Warehouse\GoodsoutNote $apiServiceSearchWarehouseGoodsoutnote,
        \Hotlink\Brightpearl\Helper\Api\Service\Order $apiServiceOrder,
        \Hotlink\Brightpearl\Helper\Api\Idset $apiIdset,
        \Hotlink\Brightpearl\Helper\Api\Service\Warehouse $apiServiceWarehouse,
        \Hotlink\Brightpearl\Model\Lookup\Shipping\MethodFactory $lookupShippingMethodFactory,
        \Hotlink\Brightpearl\Model\ShipmentFactory $shipmentTrackingFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentEmailSender,

        \Hotlink\Brightpearl\Helper\Shipping $shippingHelper,

        \Hotlink\Framework\Helper\Module $moduleHelper,
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Warehouse\CollectionFactory $warehouseCollectionFactory
    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );

        $this->orderService = $orderService;
        $this->shipmentFactory = $shipmentFactory;
        $this->transactionFactory = $transactionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;

        $this->apiServiceSearchPeriod = $apiServiceSearchPeriod;
        $this->apiServiceSearchOrder = $apiServiceSearchOrder;
        $this->apiServiceSearchWarehouseGoodsoutnote = $apiServiceSearchWarehouseGoodsoutnote;
        $this->apiServiceOrder = $apiServiceOrder;
        $this->apiIdset = $apiIdset;
        $this->apiServiceWarehouse = $apiServiceWarehouse;
        $this->lookupShippingMethodFactory = $lookupShippingMethodFactory;
        $this->shipmentTrackingFactory = $shipmentTrackingFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->shipmentEmailSender = $shipmentEmailSender;
        $this->shippingHelper = $shippingHelper;

        $this->moduleHelper = $moduleHelper;
        $this->warehouseCollectionFactory = $warehouseCollectionFactory;
    }

    protected function apiSearchOrders( $lookbehindDate, $pageSize, $first )
    {
        $report = $this->getReport();

        $date = new \DateTime( $lookbehindDate, new \DateTimeZone( 'UTC' ) );
        $updatedOn = $this->apiServiceSearchPeriod->toAfter( $date );

        $report->info( 'Searching for orders where updatedOn = ' . $updatedOn )->indent();

        $result = $this->_apiSearch( $this->apiServiceSearchOrder,
                                     array( 'updatedOn' => $updatedOn ),
                                     $pageSize,
                                     $first,
                                     'updatedOn',
                                     'DESC' );
        $report->unindent();

        return $result;
    }

    protected function apiSearchGoodsOutNotes( $lookbehindDate, $pageSize, $first )
    {
        $report = $this->getReport();

        $env     = $this->getEnvironment();
        $storeId = $env->getStoreId();
        $config  = $env->getConfig();
        $channel = $config->getChannel( $storeId );

        $date = new \DateTime( $lookbehindDate, new \DateTimeZone( 'UTC' ) );
        $createdOn = $this->apiServiceSearchPeriod->toAfter( $date );

        $report->info( 'Searching for goods-out notes where createdOn= '.$createdOn.', and channel = '.$channel )->indent();

        $result = $this->_apiSearch( $this->apiServiceSearchWarehouseGoodsoutnote,
                                     array( 'createdOn' => $createdOn,
                                            'channel'   => $channel ),
                                     $pageSize,
                                     $first,
                                     $config->getSortBy( $storeId ),
                                     $config->getSortDirection( $storeId ) );

        $report->unindent();

        return $result;
    }

    protected function _apiSearch( $api, $filters, $pageSize, $first, $sortBy, $sortDirection )
    {
        $report = $this->getReport();

        $env     = $this->getEnvironment();
        $config  = $env->getConfig();

        $storeId = $env->getStoreId();
        $account = $env->getAccountCode();

        $report->debug( "pageSize=$pageSize, firstResult=$first" );

        $result = $report($api, 'search', $storeId, $account, $filters, $pageSize, $first, $sortBy, $sortDirection);

        $pag  = $result->getPagination();
        $nr   = $pag->getData('resultsReturned');
        $more = $pag->getData('morePagesAvailable') ? 'Yes' : 'No';

        $report->debug( "results returned=$nr, more available=$more" );

        return $result;
    }


    protected function apiGetOrderNotes( $noteType, $orderId, $noteId )
    {
        $report      = $this->getReport();

        $orderIdSet = is_array( $orderId )
            ? $this->apiIdset->unorderedList( $orderId )
            : ( is_null( $orderId )
                ? \Hotlink\Brightpearl\Helper\Api\Idset::ALL
                : $this->apiIdset->single( $orderId ) );

        $noteIdSet = is_array( $noteId )
            ? $this->apiIdset->unorderedList( $noteId )
            : ( is_null( $noteId )
                ? null
                : $this->apiIdset->single( $noteId ) );

        $report->info( "Fetching order [$orderIdSet] shipping notes [$noteIdSet]" )->indent();

        $notes = $report( $this->apiServiceWarehouse,
                          $this->getNoteApiMethod( $noteType ),
                          $this->getEnvironment()->getStoreId(),
                          $this->getEnvironment()->getAccountCode(),
                          $orderIdSet,
                          $noteIdSet );
        $nr = count($notes);
        $report->debug( "results returned=$nr" )->unindent();

        return ( $notes )
            ? ( ( is_scalar($noteId) && ( is_null($orderId) || is_scalar($orderId) ) )
                ? array_shift($notes)
                : $notes )
            : null;
    }

    protected function apiGetOrder( $orderId )
    {
        $report      = $this->getReport();

        $idSet = is_array( $orderId )
            ? $this->apiIdset->unorderedList( $orderId )
            : $this->apiIdset->single( $orderId );

        $report->info( "Fetching orders [$idSet]" )->indent();

        $orders = $report( $this->apiServiceOrder,
                           'getOrders',
                           $this->getEnvironment()->getStoreId(),
                           $this->getEnvironment()->getAccountCode(),
                           $idSet );

        $nr = count($orders);
        $report->debug( "results returned=$nr" )->unindent();

        return ( $orders )
            ? ( is_array( $orderId )
                ? $orders
                : array_shift($orders) )
            : null;
    }

    protected function importNote( $noteId,
                                   \Hotlink\Brightpearl\Model\Shipment $trackingRecord,
                                   \Hotlink\Brightpearl\Model\Platform\Data $bpNote,
                                   \Hotlink\Brightpearl\Model\Platform\Data $bpOrder,
                                   \Magento\Sales\Model\Order $mageOrder,
                                   $notify,
                                   $sleep = 0 )
    {
        $report = $this->getReport();

        $report->info('Importing note ['.$noteId.'],'.
                      ' bp order ['.$bpOrder->getId().'],'.
                      ' magento order ['.$mageOrder->getIncrementId().']')->indent();

        if ( $mageOrder->canShip() )
            {
                if ( $mapping = $this->mapQtys( $bpNote, $bpOrder, $mageOrder ) )
                    {
                        //
                        //  extract quantities shipped
                        //
                        $qtys = array();
                        foreach ( $mapping as $orderItemId => $item )
                            {
                                $qtys[ $orderItemId ] = $item[ 'quantity' ];
                                if ( $item[ 'children' ] )
                                    {
                                        $item[ 'mageItem' ]->setIsQtyDecimal( true );
                                    }
                            }

                        //
                        //  prepare tracking info
                        //
                        $tracking = $this->getTrackingInfo( $bpNote, $mageOrder->getStoreId() );
                        $tracking = ( count( $tracking ) == 0 ) ? null : [ $tracking ];

                        //
                        //  Permit plugins to run (to set MSI source if required)
                        //
                        $this->processNoteHook( $bpNote, $this->getWarehouses() );

                        //
                        //  allow magento to create shipment as normal
                        //
                        $shipment = $this->shipmentFactory->create( $mageOrder, $qtys, $tracking );

                        //
                        //  add additional data for bundle items
                        //
                        $bpNoteItems = $bpNote->getData( 'orderRows' );
                        foreach ( $shipment->getAllItems() as $shipmentItem )
                            {
                                $orderItemId = $shipmentItem->getOrderItemId();
                                $mappedItem = $mapping[ $orderItemId ];
                                if ( $mappedItem[ 'children' ] )
                                    {
                                        $details = array();
                                        foreach ( $mappedItem[ 'children' ] as $bpOrderItemId => $bpOrderItem )
                                            {
                                                $qtyShipped = $this->getNoteItemQuantity( $bpNoteItems[ $bpOrderItemId ] );
                                                $details[] = array(
                                                    'qty_ordered' => $bpOrderItem[ 'quantity' ][ 'magnitude' ],
                                                    'qty_shipped' => $qtyShipped,
                                                    'ord'         => $bpOrderItem[ 'orderRowSequence' ],
                                                    'bpid'        => $bpOrderItem[ 'productId' ],
                                                    'sku'         => $bpOrderItem[ 'productSku' ],
                                                    'name'        => $bpOrderItem[ 'productName' ] );
                                            }
                                        $persist = array( 'Brightpearl' => array( 'BundleItems' => $details ) );
                                        $shipmentItem->setAdditionalData( serialize( $persist ) );
                                    }
                            }

                        //
                        //  save shipment
                        //
                        $success = $this->saveShipment( $shipment, $notify );
                        if ( $success )
                            {
                                $this->saveTracking($shipment, $trackingRecord );
                            }
                        if ( $sleep > 0 )
                            {
                                usleep( $sleep );
                            }
                    }
                else
                    {
                        $report->debug('No qtys to create shipment from');
                    }
            }
        else
            {
                $report->debug( "Magento does not allow this order to be shipped. canShip returned false!" );
            }
        $report->unindent();
    }

    public function getWarehouses()
    {
        if ( ! $this->warehouses )
            {
                $collection = $this->warehouseCollectionFactory->create();
                $this->warehouses = $collection->load()->getItems();
            }
        return $this->warehouses;
    }

    //
    //  Provides a convenient interception point for plugins
    //
    public function processNoteHook( $bpNote, $warehouses )
    {
    }

    protected function mapQtys( \Hotlink\Brightpearl\Model\Platform\Data $note,
                                \Hotlink\Brightpearl\Model\Platform\Data $bpOrder,
                                \Magento\Sales\Model\Order $mageOrder )
    {
        $result = [];
        $report = $this->getReport();
        $report->info( 'Preparing shipment qtys' )->indent();

        $bpOrderItems = $bpOrder->getData( 'orderRows' );
        $bpNoteItems = $note->getData( 'orderRows' );

        //
        //  Label all order items with their id (to identify without indexers)
        //
        foreach ( $bpOrderItems as $bpOrderItemId => $bpOrderItem )
            {
                $bpOrderItem[ 'id' ] = $bpOrderItemId;
            }

        //
        //  Establish relationships between Magento items, and Brightpearl items (and bundle items)
        //
        foreach ( $bpNoteItems as $bpOrderItemId => $bpItemBatches )
            {
                //
                // 1. Identify the corresponding Brightpearl order item
                //
                if ( isset( $bpOrderItems[ $bpOrderItemId ] ) )
                    {
                        $bpOrderItem = $bpOrderItems[ $bpOrderItemId ];
                        $bpOrderItemRoot = $this->findRoot( $bpOrderItem, $bpOrderItems );

                        //
                        //  2. Identify Magento order item
                        //
                        $externalRef = $bpOrderItemRoot[ 'externalRef' ];
                        $mageItem = $mageOrder->getItemById( $externalRef );

                        if ( $mageItem )
                            {
                                //
                                //  Create mapping
                                //
                                $mageItemId = $mageItem->getId();
                                if ( ! isset( $result[ $mageItemId ] ) )
                                    {
                                        $result[ $mageItemId ] = array
                                                               ( 'mageItem'   => $mageItem,
                                                                 'bpItem'     => $bpOrderItemRoot,
                                                                 'children'   => array(),
                                                                 'quantity'   => null
                                                               );
                                    }
                                if ( $this->isChild( $bpOrderItem ) )
                                    {
                                        $map = $result[ $mageItemId ];
                                        $map[ 'children' ][ $bpOrderItemId ] = $bpOrderItem;
                                        $result[ $mageItemId ] = $map;
                                    }
                            }
                        else
                            {
                                $report->error( "BP note row [$bpOrderItemId] has no external ref (Magento row id), perhaps it was added manually within Brightpearl?" );
                            }
                    }
                else
                    {
                        $report->error( "BP note row [$bpOrderItemId] missing from BP order" );
                    }
            }

        $result2 = array();
        //
        //  Calculate quantities of identified items
        //
        foreach ( $result as $itemId => $item )
            {
                $qtyShipped = 0;
                $qtyOrdered = $item[ 'bpItem' ][ 'quantity' ][ 'magnitude' ];
                if ( $item[ 'children' ] )
                    {
                        //
                        //  calculate bundle quantities ordered
                        //
                        $qtyBundled = 0;
                        foreach ( $bpOrderItems as $bpOrderItemId => $bpOrderItem )
                            {
                                $bpOrderItemRoot = $this->findRoot( $bpOrderItem, $bpOrderItems );
                                $externalRef = $bpOrderItemRoot[ 'externalRef' ];
                                //
                                // only consider leaf nodes, so exclude anything with a parent
                                //
                                if ( ! $this->isParent( $bpOrderItem ) )
                                    {
                                        //
                                        // given a tree structure
                                        //
                                        //   SKU                                    Quantities
                                        //
                                        //   TOPLEVELBUNDLE                         1 ordered
                                        //       COMPONENTLEVELBUNDLE               2 contained in TOPLEVELBUNDLE
                                        //          simpleSku                       5 contained in COMPONENTLEVELBUNDLE
                                        //
                                        //   quantity of simpleSku ordered is the product of parents ordered amounts
                                        //
                                        //   ie. Total ordered =
                                        //           (1 * TOPLEVELBUNDLE ordered)
                                        //         * (2 * COMPONENTLEVELBUNDLE constituents)
                                        //         * (5 * simpleSku constituents)
                                        //
                                        if ( $itemId == $externalRef )
                                            {
                                                $qtyOrderedLeaf = $bpOrderItem[ 'quantity' ][ 'magnitude' ];
                                                $qtyBundled += $qtyOrderedLeaf;
                                            }
                                    }
                            }
                        //
                        //  calculate bundle quantities shipped
                        //
                        foreach ( $item[ 'children' ] as $bpOrderItemId => $bpOrderItem )
                            {
                                $bpNoteItem = $bpNoteItems[ $bpOrderItemId ];
                                $qtyShipped += $this->getNoteItemQuantity( $bpNoteItem );
                            }
                        //
                        //  calculate proportion shipped
                        //
                        $quantity = ( $qtyBundled > 0 )
                                  ? $qtyOrdered * $qtyShipped / $qtyBundled
                                  : 0;
                    }
                else
                    {
                        $bpOrderItemId = $item[ 'bpItem' ][ 'id' ];
                        $bpNoteItem = $bpNoteItems[ $bpOrderItemId ];
                        $qtyShipped = $this->getNoteItemQuantity( $bpNoteItem );
                        $quantity = $qtyShipped;
                    }
                $item[ 'quantity' ] = $quantity;
                $result2[ $itemId ] = $item;
            }

        $report->unindent();
        return $result2;

    }

    protected function mapQtysM2( \Hotlink\Brightpearl\Model\Platform\Data $note,
                                \Hotlink\Brightpearl\Model\Platform\Data $bpOrder,
                                \Magento\Sales\Model\Order $mageOrder )
    {
        $report = $this->getReport();
        $report->info('Preparing shippment qtys')->indent();

        $bpMageOrderItemMap = $this->getBpMageOrderItemMap($bpOrder, $mageOrder);

        $shipmentQtys = array();
        foreach ($note->getData('orderRows') as $_bpOrderRowId => $_rowBatches) {

            if (!isset($bpMageOrderItemMap[$_bpOrderRowId])) {
                continue;
            }

            $map          = $bpMageOrderItemMap[$_bpOrderRowId];
            $orderItem    = $map->getMageOrderItem();
            $orderItemId  = $orderItem->getId();
            $orderItemSku = $orderItem->getSku();
            $qtyAvailable = $orderItem->getQtyToShip();

            $report
                ->debug('Processing: BP item id=['.$_bpOrderRowId.']; '.
                        'Magento item id=['.$orderItemId.'], sku=['.$orderItemSku.']')->indent();

            foreach ($_rowBatches as $_batch) {

                $qtyToShip = $_batch->getQuantity();

                if ( $bpParentRowId = $map->getBpParentRowId() ) {  // BP bundle product

                    $parentMap           = $bpMageOrderItemMap[$bpParentRowId];
                    $mageParentOrderItem = $parentMap->getMageOrderItem();

                    if ( !$this->isShipmentSeparately( $mageParentOrderItem ) ) {
                        $qtyAvailable = $mageParentOrderItem->getQtyToShip();
                        $orderItemId  = $mageParentOrderItem->getId();

                        $report->debug('Item cannot be shipped separately due to bundle shipping settings = "together".'.
                                       ' Shipping parent item with id ['.$orderItemId.'] instead!');
                    }
                }

                $accumulated  = isset( $shipmentQtys[$orderItemId] ) ? $shipmentQtys[$orderItemId] : 0;
                $qtyAvailable = max(0, $qtyAvailable - $accumulated);

                if ($qtyAvailable == 0) {
                    $report->debug('Skipping, available qty to ship is 0 (zero).');
                }
                else {
                    $minToShip = min( $qtyAvailable, $qtyToShip );

                    if ($qtyAvailable < $qtyToShip) {
                        $report->debug('BP item qty ['.$qtyToShip.'] is grater than Magento item qty available ['.
                                       $qtyAvailable.'], therefore minimum qty ['.$minToShip.'] is considered!');
                    }

                    $shipmentQtys[ $orderItemId ] = $accumulated + $minToShip;

                    $report->debug('Mapped qty = '.$minToShip);
                }

            }
            $report->unindent();
        }

        $report->unindent();

        return $shipmentQtys;
    }

    protected function mapQtys2M1( Flint_Brightpearl_Model_Platform_Data $note,
                                 Flint_Brightpearl_Model_Platform_Data $bpOrder,
                                 Mage_Sales_Model_Order $mageOrder )
    {
        $result = array();
        $report = $this->getReport();
        $report->info( 'Preparing shippment qtys' )->indent();

        $bpOrderItems = $bpOrder->getData( 'orderRows' );
        $bpNoteItems = $note->getData( 'orderRows' );

        //
        //  Label all order items with their id (to identify without indexers)
        //
        foreach ( $bpOrderItems as $bpOrderItemId => $bpOrderItem )
            {
                $bpOrderItem[ 'id' ] = $bpOrderItemId;
            }

        //
        //  Establish relationships between Magento items, and Brightpearl items (and bundle items)
        //
        foreach ( $bpNoteItems as $bpOrderItemId => $bpItemBatches )
            {
                //
                // 1. Identify the corresponding Brightpearl order item
                //
                if ( isset( $bpOrderItems[ $bpOrderItemId ] ) )
                    {
                        $bpOrderItem = $bpOrderItems[ $bpOrderItemId ];
                        $bpOrderItemRoot = $this->findRoot( $bpOrderItem, $bpOrderItems );

                        //
                        //  2. Identify Magento order item
                        //
                        $externalRef = $bpOrderItemRoot[ 'externalRef' ];
                        $mageItem = $mageOrder->getItemById( $externalRef );

                        //
                        //  Create mapping
                        //
                        $mageItemId = $mageItem->getId();
                        if ( ! isset( $result[ $mageItemId ] ) )
                            {
                                $result[ $mageItemId ] = array
                                                       ( 'mageItem'   => $mageItem,
                                                         'bpItem'     => $bpOrderItemRoot,
                                                         'children'   => array(),
                                                         'quantity'   => null
                                                       );
                            }
                        if ( $this->isChild( $bpOrderItem ) )
                            {
                                $map = $result[ $mageItemId ];
                                $map[ 'children' ][ $bpOrderItemId ] = $bpOrderItem;
                                $result[ $mageItemId ] = $map;
                            }
                    }
                else
                    {
                        $report->error( "BP note row [$bpOrderItemId] missing from BP order" );
                    }
            }

        $result2 = array();
        //
        //  Calculate quantities of identified items
        //
        foreach ( $result as $itemId => $item )
            {
                $qtyShipped = 0;
                $qtyOrdered = $item[ 'bpItem' ][ 'quantity' ][ 'magnitude' ];
                if ( $item[ 'children' ] )
                    {
                        //
                        //  calculate bundle quantities ordered
                        //
                        $qtyBundled = 0;
                        foreach ( $bpOrderItems as $bpOrderItemId => $bpOrderItem )
                            {
                                $bpOrderItemRoot = $this->findRoot( $bpOrderItem, $bpOrderItems );
                                $externalRef = $bpOrderItemRoot[ 'externalRef' ];
                                if ( $itemId == $externalRef )
                                    {
                                        // exclude the parent item itself (which is not a bundle component)
                                        if ( $item[ 'bpItem' ][ 'id' ] != $bpOrderItem[ 'id' ] )
                                            {
                                                $qtyBundled += $bpOrderItem[ 'quantity' ][ 'magnitude' ];
                                            }
                                    }
                            }
                        //
                        //  calculate bundle quantities shipped
                        //
                        foreach ( $item[ 'children' ] as $bpOrderItemId => $bpOrderItem )
                            {
                                $bpNoteItem = $bpNoteItems[ $bpOrderItemId ];
                                $qtyShipped += $this->getNoteItemQuantity( $bpNoteItem );
                            }
                        //
                        //  calculate proportion shipped
                        //
                        $quantity = ( $qtyBundled > 0 )
                                  ? $qtyOrdered * $qtyShipped / $qtyBundled
                                  : 0;
                    }
                else
                    {
                        $bpNoteItem = $bpNoteItems[ $bpOrderItemId ];
                        $qtyShipped = $this->getNoteItemQuantity( $bpNoteItem );
                        $quantity = $qtyShipped;
                    }
                $item[ 'quantity' ] = $quantity;
                $result2[ $itemId ] = $item;
            }

        $report->unindent();
        return $result2;
    }

    protected function getNoteItemQuantity( $bpNoteItem )
    {
        $qty = 0;
        foreach ( $bpNoteItem as $batch )
            {
                $qty += $batch[ 'quantity' ];
            }
        return $qty;
    }

    protected function findRoot( $bpOrderItem, $bpOrderItems )
    {
        while ( $this->isChild( $bpOrderItem ) )
            {
                $parentItemId = $bpOrderItem[ 'composition' ][ 'parentOrderRowId' ];
                $bpOrderItem = $bpOrderItems[ $parentItemId ];
            }
        return $bpOrderItem;
    }

    protected function isChild( $row )
    {
        return ! is_null ( $row ) && ! is_null( $row[ 'composition' ] ) && $row[ 'composition' ][ 'bundleChild' ];
    }

    protected function isParent( $row )
    {
        return ! is_null ( $row ) && ! is_null( $row[ 'composition' ] ) && $row[ 'composition' ][ 'bundleParent' ];
    }

    protected function saveShipment( \Magento\Sales\Model\Order\Shipment $shipment, $sendEmail )
    {
        $report = $this->getReport();
        $order  = $shipment->getOrder();

        $this->register( $shipment );

        // https://github.com/magento/magento2/issues/4320
        $shipment->setEmailSent($sendEmail);

        $order->setCustomerNoteNotify($sendEmail);

        $transaction = $this->transactionFactory->create();
        $transaction->addObject($shipment);
        $transaction->addObject($order);

        $error = false;
        try {

            $transaction->save();

            $report->info('Shipment was created successfully')->incsuccess();
        }
        catch ( \Exception $e) {
            $error = true;
            $report->error('There was a problem creating the shipment: '. $e->getMessage())->incfail();
        }

        if ($sendEmail && !$error) {
            $this->shipmentEmailSender->send( $shipment );
        }

        return !$error;
    }

    public function register( $shipment )
    {
        if ($shipment->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We cannot register an existing shipment')
            );
        }

        $totalQty = 0;

        /** @var \Magento\Sales\Model\Order\Shipment\Item $item */
        foreach ($shipment->getAllItems() as $item) {
            if ($item->getQty() > 0) {
                $item->register();

                if (!$item->getOrderItem()->isDummy(true)) {
                    $totalQty += $item->getQty();
                }
            }
        }

        $shipment->setTotalQty($totalQty);

        return $shipment;
    }

    protected function saveTracking( \Magento\Sales\Model\Order\Shipment $shipment,
                                     \Hotlink\Brightpearl\Model\Shipment $trackingRecord)
    {
        $report = $this->getReport();

        $trackingRecord->setShipmentId( $shipment->getId() );
        $trackingRecord->setCreatedAt( gmdate( \Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT ) );

        try {
            $trackingRecord->save();
            $report->debug( 'Shipment tracking record was created successfully' );
        }
        catch ( \Exception $e ) {
            $report->error( 'There was a problem creating the shipment tracking record: '. $e->getMessage() );
        }
    }

    protected function getTrackingInfo( \Hotlink\Brightpearl\Model\Platform\Data $note, $storeId )
    {
        $report = $this->getReport();
        $report->info( 'Adding tracking information' )->indent();

        // Load local BP data
        $bpShippingMethodId = false;
        $bpShippingMethod = false;
        if ( $bpShippingMethodId = $note->getShipping()->getData('shippingMethodId') )
            {
                $report->debug( "Note identifies BP shipping method id '$bpShippingMethodId'" );
                $bpShippingMethod = $this
                                  ->lookupShippingMethodFactory
                                  ->create()
                                  ->load( $bpShippingMethodId, 'brightpearl_id' );
                if ( $bpShippingMethod->getId() )
                    {
                        $bpMethodName = $bpShippingMethod->getName();
                        $report->debug( "Using BP method '$bpMethodName'" );
                    }
                else
                    {
                        $report->warn( "No BP method with id '$bpShippingMethodId' in database (try resyncing)" );
                    }
            }
        else
            {
                $report->warn( 'BP shipping note is missing shipping method' );
            }

        $carrierCode = false;
        $number = false;
        $title = false;

        // Detect carrier
        if ( $bpShippingMethodId )
            {
                $carrierCode = $this->getMagentoCarrier( $bpShippingMethodId, $storeId );
                if ( $carrierCode )
                    {
                        $report->debug( "Using magento carrier '$carrierCode'" );
                    }
                else
                    {
                        $report
                            ->warn( "No Magento carrier configured for BP shipping method '$bpShippingMethodId'" );
                    }
            }
        else
            {
                $report->warn( "Unable to determine carrier (no BP method id)" );
            }

        // Detect tracking number
        $number = $note->getShipping()->getData( 'reference' );
        if ( $number )
            {
                $report->debug( "Extracted tracking reference '$number'" );
            }
        else
            {
                $report->warn( 'BP Shipping note is missing "reference" field' );
            }

        // Detect title
        if ( $bpShippingMethod && $bpShippingMethod->getId() )
            {
                $title = $bpShippingMethod->getName();
                if ( $bpCode = $bpShippingMethod->getCode() )
                    {
                        $title .= " (" . $bpCode . ")";
                    }
                if ( $title )
                    {
                        $report->debug( "Identified tracking note title as '$title'" );
                    }
                else
                    {
                        $report->warn( "Unable to identify title, BP shipping method is name / code data" );
                    }
            }
        else
            {
                $report->warn( "Unable to extract title (due to missing BP method info)" );
            }

        // Prepare tracking data
        $tracking = [];
        if ( $carrierCode || $number || $title )
            {
                if ( !$carrierCode )
                    {
                        $carrierCode = self::MISSING_CARRIER;
                        $report->warn( "Using carrier code '$carrierCode'" );
                    }
                if ( !$number )
                    {
                        $number = self::MISSING_NUMBER;
                        $report->warn( "Using tracking reference '$number'" );
                    }
                $tracking =  [ 'carrier_code' => $carrierCode,
                               'title'        => $title,
                               'number'       => $number ];
                $report->info( "Using ( carrier, title, number ) = ( '$carrierCode', '$title', '$number' )" );
            }
        else
            {
                $report->warn( "Insufficient tracking info available" );
            }
        $report->unindent();
        return $tracking;
    }

    protected function getBpMageOrderItemMap( \Hotlink\Brightpearl\Model\Platform\Data $bpOrder,
                                              \Magento\Sales\Model\Order $mageOrder )
    {
        $report             = $this->getReport();
        $bpOrderRows        = $bpOrder->getData('orderRows');
        $bpMageOrderItemMap = array();

        foreach ($bpOrderRows as $bpOrderRowId => $bpOrderRow) {
            if ( $extRef = $bpOrderRow->getData('externalRef') ) {
                if ( $mageOrderItem = $mageOrder->getItemById($extRef) ) {

                    $map = $this->dataObjectFactory->create();

                    $map->setMageOrderItem($mageOrderItem);

                    $composition = $bpOrderRow->getData('composition');

                    // this can return zero rather than null
                    $parentRowId = isset($composition['parentOrderRowId'])
                        ? $composition['parentOrderRowId']
                        : null;

                    $map->setBpParentRowId($parentRowId);

                    $bpMageOrderItemMap[$bpOrderRowId] = $map;
                }
                else {
                    $report->warn('Order item with id ['.$extRef.'] was not found in Magento');
                }
            }
            else {
                $report->warn('BP Order Item with id ['.$bpOrderRowId.'] is missing externalRef field');
            }
        }

        return $bpMageOrderItemMap;
    }

    protected function isShipmentSeparately( \Magento\Sales\Model\Order\Item $item)
    {
        if ($options = $item->getProductOptions()) {
            return (
                isset($options['shipment_type']) and
                $options['shipment_type'] == \Magento\Catalog\Model\Product\Type\AbstractType::SHIPMENT_SEPARATELY
                );
        }
    }

    protected function loadMagentoOrder( $incrementId  )
    {
        $report = $this->getReport();

        $info = is_array( $incrementId )
            ? implode( ',', $incrementId )
            : $incrementId;

        $report->info('Loading Magento orders ['.$info.']')->indent();

        $orders = $this->orderCollectionFactory->create()
            ->addFieldToFilter( 'increment_id', array( 'in' => $incrementId ) )
            ->load();

        $nr = $orders->count();
        $report->debug( "results found=$nr" )->unindent();

        return ( $nr > 0 )
            ? ( is_array( $incrementId )
                ? $orders
                : $orders->getItemByColumnValue( 'increment_id', $incrementId ) )
            : null;
    }

    protected function getMagentoCarrier( $bpShippingMethodId, $storeId )
    {
        $carrier = null;
        $map     = $this->getEnvironment()->getConfig()->getShippingOptionsMap($storeId);

        foreach ($map as $row) {

            $brightpearl = isset($row['brightpearl']) ? $row['brightpearl'] : null;
            $magento     = isset($row['magento'])     ? $row['magento']     : null;

            if ( $bpShippingMethodId == $brightpearl ) {
                $carrier = $magento;
                break;
            }
        }

        if ( ( strlen( $carrier ) > 0 ) )
            {
                $carrier = $this->shippingHelper->decodeCarrier( $carrier );
            }
        return $carrier;
    }

    protected function getNoteApiMethod( $noteType )
    {
        $method = null;

        switch ($noteType) {
        case \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Shipment\Type::GOODS_OUT:
            $method = 'getGoodsoutNote';
            break;
        case \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Shipment\Type::DROP_SHIP:
            $method = 'getDropshipNote';
            break;
        }

        return $method;
    }

    protected function shipAllOrderItems( \Magento\Sales\Model\Order $mageOrder, $notify )
    {
        $report = $this->getReport();

        $qtys = array();
        foreach ( $mageOrder->getAllItems() as $item ) {

            if ( $toShip = $item->getQtyToShip() ) {
                $qtys[ $item->getId() ] = $toShip;
            }
        }

        if ($qtys) {
            $report->debug( 'creating shipment for all remaining items and qtys' );
            $shipment = $this->shipmentFactory->create( $mageOrder, $qtys );
            $this->saveShipment( $shipment, $notify );
        }

        return $this;
    }

    protected function checkNote( \Hotlink\Brightpearl\Model\Platform\Data $note )
    {
        $report = $this->getReport();
        $valid  = true;

        $isTransfer = $note->getData('transfer') == true;
        $isShipped = false;
        if ( $status = $note->getData('status') )
            {
                $isShipped = $status->getData('shipped') == true;
            }

        if ( $isTransfer ) {
            $report->debug("Note rejected, internal transfer");
            $valid = false;
        }
        else if ( !$isShipped ) {
            $report->debug("Note rejected, not shipped yet");
            $valid = false;
        }

        return $valid;
    }

    protected function platformDataColumn( $input, $columnKey, $indexColumn = null )
    {
        if ( is_null($input) ) return $input;

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
