<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Realtime\Import;

class Implementation extends \Hotlink\Brightpearl\Model\Interaction\Order\Status\Import\Implementation\AbstractImplementation
{

    protected function _getName()
    {
        return 'Hotlink Brightpearl: Magento Sales Order Status Importer (real-time)';
    }

    function execute()
    {
        $report = $this->getReport();
        $environment = $this->getEnvironment();
        $report($environment, 'status');

        $orderId = $environment->getParameter('order_id')->getValue();
        $notify  = $environment->getParameter('notify_customer')->getValue();

        if ( is_null($orderId) || trim($orderId) == '') {
            $report->error("Order id is required");
            return;
        }
        $report->addReference($orderId);

        if ( $bpApiOrder = $this->serviceApiGetOrder( array($orderId) ) )
            {

                $incrementId     = $this->platformDataColumn( $bpApiOrder, 'externalRef', 'id' );
                $incrementId     = $incrementId ? array_shift($incrementId) : null;
                $bpOrderStatusId = $this->platformDataColumn( $bpApiOrder, array('orderStatus', 'orderStatusId'), 'externalRef' );
                $report->addReference( $incrementId );

                if ( $order = $this->getMagentoOrder($incrementId) )
                    {
                        $storeId     = $order->getStoreId();
                        $environment = $this->getOrCreateEnvironment( $storeId );

                        if ( $environment->isEnabled() )
                            {
                                $report->info("Processing order $incrementId")->indent();
                                $this->reportOrderInfo( $order );
                                if ( $this->isStateMutable($order) )
                                    {
                                        $bpStatus = $bpOrderStatusId[ $incrementId ];
                                        if ( $mageStatus = $this->getOrderStatusMapReverseLookup( $bpStatus ) )
                                            {
                                                if ( $mageStatus !== $order->getStatus() )
                                                    {
                                                        if ( $mageState = $this->getAssignedState( $mageStatus ) )
                                                            {
                                                                $this->mapOrderState( $mageState, $mageStatus, $notify, $order );
                                                                $this->saveOrder( $order );
                                                            }
                                                        else
                                                            {
                                                                $report->error("No corresponding Magento State found for status '$mageStatus'")->incFail();
                                                            }
                                                    }
                                                else
                                                    {
                                                        $report->debug("New status '$mageStatus' is identical to current status");
                                                    }
                                            }
                                        else
                                            {
                                                $report->warn( "No corresponding Magento Status found for BP status $bpStatus" );
                                            }
                                    }
                                else
                                    {
                                        $report->error( 'Current order state does not permit further status changes' )->incFail();
                                    }
                                $report->unindent();
                            }
                        else
                            {
                                $report->debug("Interaction disabled in store $storeId");
                            }
                    }
                else
                    {
                        $report->error("Order '$incrementId' not found in Magento")->incFail();
                    }
            }

        return $this;
    }

}
