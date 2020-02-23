<?php
namespace Hotlink\Brightpearl\Model\Queue;

class Order extends \Hotlink\Brightpearl\Model\Queue\AbstractQueue
{

    function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\ResourceModel\Queue\Order' );
    }

    function setParentId( $orderId )
    {
        $this->setOrderId( $orderId );
    }

    function shouldSend()
    {

        $send = false;

        if ( $this->getId() )
            {

                // not a new record

                if ( $this->getSendToBp() )
                    {

                        // record requires a send

                        $send = true;
                    }
                else
                    {
                        // record states no send
                    }
            }
        else
            {

                // new record

                $send = true;
            }
        return $send;
    }

    function getReportSection()
    {
        return 'queue (order)';
    }

}