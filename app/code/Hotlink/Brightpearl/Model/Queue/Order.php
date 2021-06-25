<?php
namespace Hotlink\Brightpearl\Model\Queue;

class Order extends \Hotlink\Brightpearl\Model\Queue\AbstractQueue
{

    public function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\ResourceModel\Queue\Order' );
    }

    public function setParentId( $orderId )
    {
        $this->setOrderId( $orderId );
    }

    public function shouldSend()
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

    public function getReportSection()
    {
        return 'queue (order)';
    }

}