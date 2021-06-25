<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Bulk\Import;

class Implementation extends \Hotlink\Brightpearl\Model\Interaction\Order\Status\Import\Implementation\AbstractImplementation
{
    protected function _getName()
    {
        return 'Hotlink Brightpearl: Magento Sales Order Status Importer (bulk)';
    }

    function execute()
    {
        $report = $this->getReport();
        $env    = $this->getEnvironment();
        $report->__invoke($env, 'status');

        $lookbehindDate = $env->getParameter('lookbehind')->getDate();
        $batch   = $env->getParameter('batch')->getValue();
        $sleep   = $env->getParameter('sleep')->getValue();
        $notify  = $env->getParameter('notify_customer')->getValue();

        if (is_null($lookbehindDate)) {
            $report->error("Lookbehind is required");
            return;
        }

        $firstResult = 1;
        do {  // search result pagination loop

            $searchResult = $this->apiSearchOrders($lookbehindDate, $batch, $firstResult);

            $pagination  = $searchResult->getPagination();
            $ordersFound = $searchResult->getResults();
            $resultsReturned = $pagination->getData('resultsReturned');
            $morePagesAvailable = $pagination->getData('morePagesAvailable');

            if ( $resultsReturned > 0 ) {

                $orderIds  = $this->platformDataColumn( $ordersFound, 'orderId' );
                $apiOrders = $this->serviceApiGetOrder( $orderIds );
                if ($apiOrders) {

                    $mageIncrementIds = $this->platformDataColumn( $apiOrders, 'externalRef', 'id' );
                    $bpOrderStatusIds = $this->platformDataColumn( $apiOrders, array('orderStatus', 'orderStatusId'), 'externalRef' );

                    $report->info( 'Loading orders in batches of '.$batch);

                    $collection = $this->initCollection( $batch, $mageIncrementIds );
                    $size  = $collection->getSize();
                    $report->indent()->debug( "results found = $size" )->unindent();

                    $pages = ( $size > 0 )
                        ? $collection->getLastPageNumber()
                        : 0;

                    if ( $pages ) {


                        $currentPage = 0;
                        while ( ++$currentPage <= $pages ) {

                            $report->info("Processing batch $currentPage of $pages")->setBatch( $currentPage )->indent();

                            $collection->clear()->setCurPage( $currentPage )->load();

                            foreach ($collection as $order) {
                                $incrementId = $order->getIncrementId();

                                $this->reportOrderInfo( $order, "Processing order $incrementId" );
                                $report->addReference($incrementId)->indent();

                                if ( $this->isStateMutable($order) ) {

                                    $bpStatus = $bpOrderStatusIds[ $incrementId ];

                                    if ( $mageStatus = $this->getOrderStatusMapReverseLookup( $bpStatus ) ) {
                                        if ( $mageStatus !== $order->getStatus() ) {
                                            if ( $mageState = $this->getAssignedState( $mageStatus ) ) {
                                                $report->addReference($mageState)->addReference($mageStatus);

                                                // isStateProtected - remove in M2 !
                                                /* if ( !$order->isStateProtected( $mageState ) ) { */

                                                    $this->mapOrderState( $mageState, $mageStatus, $notify, $order );
                                                    $this->saveOrder( $order, $sleep );
                                                /* } */
                                                /* else { */
                                                /*     $report->error("$mageState state cannot be set from outside")->incFail(); */
                                                /* } */
                                            }
                                            else {
                                                $report->error("No corresponding Magento State found for status '$mageStatus'")->incFail();
                                            }
                                        }
                                        else {
                                            $report->debug("New status '$mageStatus' is identical to current status. Order skipped.");
                                        }
                                    }
                                    else {
                                        $report->warn( "No corresponding Magento Status found for BP status [$bpStatus]" );
                                    }
                                }
                                else {
                                    $report->warn( 'Current order state does not permit further status changes' );
                                }

                                $report->unindent();
                            }

                            $report->unindent();
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

            // continue starting at last result
            $firstResult = $pagination->getData('lastResult') + 1;
        }
        while ( $morePagesAvailable );
    }
}