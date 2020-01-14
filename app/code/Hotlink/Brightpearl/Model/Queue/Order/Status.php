<?php
namespace Hotlink\Brightpearl\Model\Queue\Order;

class Status extends \Hotlink\Brightpearl\Model\Queue\AbstractQueue
{

    public function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\ResourceModel\Queue\Order\Status', 'id' );
    }

    public function setParentId( $orderId )
    {
        $this->setOrderId( $orderId );
    }

    public function getReportSection()
    {
        return 'queue (order status)';
    }

    public function shouldSend( \Hotlink\Brightpearl\Model\Queue\Order $orderTracking )
    {
        $send = false;

        if ( $orderTracking->getInBp() ) {

            if ( $this->getId() ) {
                if ( $this->getSendToBp() ) {

                    // record requires a send

                    $send = true;
                }
                else {
                    // record states no send
                }
            }
            else {

                // new record

                $send = true;
            }
        }
        else {
            // order not in brightpearl
        }

        return $send;


    }
}