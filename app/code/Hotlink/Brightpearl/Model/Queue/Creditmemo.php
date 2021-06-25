<?php
namespace Hotlink\Brightpearl\Model\Queue;

class Creditmemo extends \Hotlink\Brightpearl\Model\Queue\AbstractQueue
{

    public function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\ResourceModel\Queue\Creditmemo' );
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
        return 'queue-creditmemo';
    }

}