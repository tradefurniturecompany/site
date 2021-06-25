<?php
namespace Hotlink\Brightpearl\Model\Interaction\Shipment\Bulk\Import;

class Implementation extends \Hotlink\Brightpearl\Model\Interaction\Shipment\Implementation\AbstractImplementation
{

    const ROOT_ITEM_ID = "rootItemId";

    protected function _getName()
    {
        return 'Hotlink Brightpearl Goods-Out Note Importer (bulk)';
    }

    public function execute()
    {
        $report = $this->getReport();
        $env    = $this->getEnvironment();
        $report( $env, "status" );

        $lookbehind = $env->getParameter( 'lookbehind' )->getDate();
        $batch      = $env->getParameter( 'batch' )->getValue();
        $sleep      = $env->getParameter( 'sleep' )->getValue();

        if ( is_null( $lookbehind ) )
            {
                $report->error("Lookbehind is required");
                return;
            }

        $timestamp = strtotime( $lookbehind );

        $firstResult = 1;
        $finished = false;

        while ( ! $finished )
            {
                //
                //  1. identify all changed orders
                //
                $found = $this->apiSearchOrders( $lookbehind, $batch, $firstResult );
                $instanceId = $found->getInstanceId();

                foreach ( $found->getResults() as $order )
                    {
                        //
                        //  2. filter orders related to this installation
                        //
                        $orderInstalledInstanceId = $order->getData( 'installedIntegrationInstanceId' );
                        if ( $orderInstalledInstanceId == $instanceId )
                            {
                                //
                                //  3. Retrieve shipping notes
                                //
                                $storeId = null;  // this is passed to transport and is redundant
                                $accountCode = $env->getAccountCode();
                                $idOrder = $order->getData( 'orderId' );
                                $idSet = null;
                                $timeout = 1000;
                                $shipGood = $report( $this->apiServiceWarehouse, "getGoodsoutNote", 
                                                     $storeId,
                                                     $accountCode,
                                                     $idOrder,
                                                     $idSet,
                                                     $timeout );
                                $shipDrop = $report( $this->apiServiceWarehouse, "getDropshipNote",
                                                     $storeId,
                                                     $accountCode,
                                                     $idOrder,
                                                     $idSet,
                                                     $timeout );
                                if ( count( $shipGood ) || count( $shipDrop ) )
                                    {
                                        $bOrder = $this->apiGetOrder( $idOrder );
                                        $incrementId = $bOrder[ 'reference' ];
                                        $mOrder = $this->loadMagentoOrder( $incrementId  );
                                        $this->importNotes( $bOrder,
                                                            $mOrder,
                                                            \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Shipment\Type::GOODS_OUT,
                                                            $shipGood,
                                                            $sleep );
                                        $this->importNotes( $bOrder,
                                                            $mOrder,
                                                            \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Shipment\Type::DROP_SHIP,
                                                            $shipDrop,
                                                            $sleep );
                                    }
                            }
                    }
                $pagination = $found->getPagination();
                $firstResult = $pagination->getData( 'lastResult' ) + 1;
                $finished = ! $pagination->getData( 'morePagesAvailable' );
            }

        $this->reconcileUsingOrderShippingStatus();

    }

    protected function importNotes( $bOrder, $mOrder, $noteType, $notes, $sleep )
    {
        foreach ( $notes as $noteId => $note )
            {
                if ( isset( $note[ 'status' ][ 'shipped' ] ) && ( $note[ 'status' ][ 'shipped' ] ) )
                    {
                        $trackingRecord = $this->shipmentTrackingFactory->create();
                        $trackingRecord->load( $noteId, 'brightpearl_id' );
                        if ( ! $trackingRecord->getId() )
                            {
                                // note doesn't exist, create new shipping note
                                $trackingRecord->setBrightpearlId( $noteId );
                                $trackingRecord->setShipmentType( $noteType  );
                                $config = $this->getEnvironment()->getConfig();
                                $notify = $config->getNotifyCustomer( $mOrder->getStoreId() );
                                $this->importNote( $noteId, $trackingRecord, $note, $bOrder, $mOrder, $notify, $sleep );
                            }
                    }
            }
    }

    protected function isChild( $row )
    {
        return ! is_null ( $row ) && ! is_null( $row[ 'composition' ] ) && $row[ 'composition' ][ 'bundleChild' ];
    }

    protected function isParent( $row )
    {
        return ! is_null ( $row ) && ! is_null( $row[ 'composition' ] ) && $row[ 'composition' ][ 'bundleParent' ];
    }

    protected function reconcileUsingOrderShippingStatus()
    {
        $report = $this->getReport();
        $env    = $this->getEnvironment();
        $config = $env->getConfig();

        $lookbehind = $env->getParameter('lookbehind')->getDate();
        $batch      = $env->getParameter('batch')->getValue();
        $sleep      = $env->getParameter('sleep')->getValue();

        $report->info("Starting 'Shipping notes reconciliation using order shipping status'");

        $firstResult = 1;
        do {

            $result = $this->apiSearchOrders($lookbehind, $batch, $firstResult);

            $pagination  = $result->getPagination();
            $ordersFound = $result->getResults();
            $resultsReturned    = $pagination->getData('resultsReturned');
            $morePagesAvailable = $pagination->getData('morePagesAvailable');

            if ( $resultsReturned > 0 ) {

                // 1. get order ids
                $orderIds  = $this->platformDataColumn( $ordersFound, 'orderId' );
                if ( $apiOrders = $this->apiGetOrder( $orderIds ) ) {

                    // 2. filter response.shippingStatusCode
                    if ( $qualifiedApiOrders = $this->filterShippingStatusCode( $apiOrders ) ) {

                        // 3. load magento orders
                        $mageOrderIncrementIds = $this->platformDataColumn( $qualifiedApiOrders, 'externalRef', 'id' );
                        $mageOrders = $this->loadMagentoOrder( $mageOrderIncrementIds );

                        if ($mageOrders) {
                            $incrementIdsToBpOrderIdIndex = array_flip( $mageOrderIncrementIds );

                            // 4. can ship ?
                            $mageOrders = $this->filterCanShip( $mageOrders );

                            foreach ($mageOrders as $mageOrder) {
                                $incrementId = $mageOrder->getIncrementId();
                                $notify      = $config->getNotifyCustomer( $mageOrder->getStoreId() );
                                $bpOrderId   = $incrementIdsToBpOrderIdIndex[ $incrementId ];
                                $bpOrder     = $apiOrders[ $bpOrderId ];

                                $report->info("Processing order $incrementId")->indent();

                                $bpOrder[ 'shippingStatusCode' ] = 'ASS';

                                // 5. case on shippingStatusCode
                                switch( $bpOrder[ 'shippingStatusCode' ] ) {

                                case 'NST':
                                    //  5.1 create shipment for all remaining items
                                    $report->debug('shippingStatusCode=NST (No stock tracked products on order)');
                                    $this->shipAllOrderItems( $mageOrder, $notify );
                                    break;

                                case 'ASS':
                                    //  6.1 get order shipping notes
                                    $report->debug( 'order.shippingStatusCode=ASS (All stock shipped)' );

                                    $apiGoodsOutNotes = $this->apiGetOrderNotes(
                                        \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Shipment\Type::GOODS_OUT,
                                        $bpOrderId,
                                        null );

                                    $apiDropShipNotes = $this->apiGetOrderNotes(
                                        \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Shipment\Type::DROP_SHIP,
                                        $bpOrderId,
                                        null );

                                    //  6.2 call function to import these notes
                                    if ( $apiGoodsOutNotes ) {
                                        $newApiGoodsOutNotes = $this->filterNewNotes(
                                            array_keys( $apiGoodsOutNotes ),
                                            \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Shipment\Type::GOODS_OUT );

                                        if ( $newApiGoodsOutNotes ) {
                                            $goodsOutNotes = array_intersect_key( $apiGoodsOutNotes,
                                                                                  array_flip( $newApiGoodsOutNotes ) );
                                            $this->importNotes(
                                                \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Shipment\Type::GOODS_OUT,
                                                $goodsOutNotes,
                                                $apiOrders,
                                                $mageOrders,
                                                $sleep );
                                        }
                                    }

                                    if ( $apiDropShipNotes ) {
                                        $newApiDropShipNoteIds = $this->filterNewNotes(
                                            array_keys( $apiDropShipNotes ),
                                            \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Shipment\Type::DROP_SHIP );

                                        if ( $newApiDropShipNoteIds ) {

                                            $dropShipNotes = array_intersect_key( $apiDropShipNotes, array_flip( $newApiDropShipNoteIds ) );

                                            $this->importNotes(
                                                \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Shipment\Type::DROP_SHIP,
                                                $dropShipNotes,
                                                $apiOrders,
                                                $mageOrders,
                                                $sleep );
                                        }

                                    }

                                    //  6.3 create shipment for all remaining items
                                    $this->shipAllOrderItems( $mageOrder, $notify );
                                    break;
                                }

                                $report->unindent();
                            }
                        }
                    }
                    else {
                        $report->debug( "No orders found that satisfy these filters." );
                    }
                }
                else {
                    $report->debug( "No orders returned by the API" );
                }
            }

            $firstResult = $pagination->getData('lastResult') + 1;

        } while ($morePagesAvailable);

        $report->info("Ending 'Shipping notes reconciliation using order shipping status'");

        return $this;
    }

    protected function filterValidNotes( $apiNotes )
    {
        return is_null( $apiNotes )
            ? null
            : array_filter( $apiNotes, array($this, 'checkNote') );
    }

    protected function filterShippingStatusCode( array $apiOrders )
    {
        $report = $this->getReport();
        $report->info( 'Filtering bp order with shippingStatusCode NST or ASS' );

        $result = array();
        foreach ( $apiOrders as $_id => $_order ) {
            if ( $_order->getData( 'shippingStatusCode' ) == 'NST' ||
                 $_order->getData( 'shippingStatusCode' ) == 'ASS' ) {
                $result[ $_id ] =  $_order;
            }
        }

        if (count($result) == 0) {
            $report->indent()->debug('No orders qualified')->unindent();
        }

        return $result;
    }

    protected function filterCanShip( $collection )
    {
        $report = $this->getReport();
        $report->info( 'Filtering orders that can ship' );

        foreach ($collection as $order) {
            if ( !$order->canShip() ) {
                $collection->removeItemByKey( $order->getId() );
            }
        }

        if ( $collection->count() == 0) {
            $report->indent()->debug('No orders qualified')->unindent();
        }

        return $collection;
    }

    protected function filterNewNotes(array $bpNotesIds, $shipmentType)
    {
        $report = $this->getReport();
        $report->info('Filtering notes not already imported')->indent();

        $collection = $this->shipmentTrackingFactory->create()->getCollection();
        $collection->addFieldToFilter( 'shipment_type', $shipmentType);

        $collection->addFieldToFilter( 'brightpearl_id', array('in' => $bpNotesIds) );
        $collection->load();

        $bpIds = $collection->walk('getBrightpearlId');

        $newIds = array_diff($bpNotesIds, $bpIds);

        $wanted = count($bpNotesIds);
        $left   = count($newIds);

        if ($left == 0) {
            $report->debug('No new notes');
        }
        else {
            $report->debug($left. ' out of '.$wanted .' new notes');
        }

        $report->unindent();

        return $newIds;
    }

    protected function initCollection( $batch, array $incrementIdsFilter )
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToFilter('increment_id', array('in' => $incrementIdsFilter));
        $collection->setPageSize($batch);

        return $collection;
    }
}
