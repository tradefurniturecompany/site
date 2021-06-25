<?php
namespace Hotlink\Brightpearl\Model\Queue;

class Payment extends \Hotlink\Brightpearl\Model\Queue\AbstractQueue
{

    public function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\ResourceModel\Queue\Payment' );
    }

    public function setParentId( $paymentId )
    {
        $this->setPaymentId( $paymentId );
    }

    public function shouldSend( \Hotlink\Brightpearl\Model\Queue\Order $orderTracking )
    {
        $send = false;

        if ( $orderTracking->getInBp() )
            {

                // order is already in BP

                if ( $this->getId() )
                    {

                        // payment queue record is not new

                        if ( $this->getSendToBp() )
                            {

                                // record denotes send

                                $send = true;
                            }
                        else
                            {

                                // record does not denote send

                            }
                    }
                else
                    {

                        // payment queue record is new

                        $send = true;
                    }
            }
        else
            {
                // do not send  payment without corresponding order in BP
            }
        return $send;
    }

    public function getReportSection()
    {
        return 'queue (payment)';
    }

}