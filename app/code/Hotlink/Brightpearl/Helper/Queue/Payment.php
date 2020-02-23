<?php
namespace Hotlink\Brightpearl\Helper\Queue;

class Payment extends \Hotlink\Brightpearl\Helper\Queue\AbstractQueue
{

    protected $factory;

    function __construct(
        \Hotlink\Brightpearl\Model\Queue\PaymentFactory $factory
    )
    {
        $this->factory = $factory;
    }

    function update( $object, $inBP, $sendToBP, $sentAt = null, $lastAmount = null )
    {
        if ( !is_null( $lastAmount ) )
            {
                $object->setLastAmount( $lastAmount );
            }
        return parent::update( $object, $inBP, $sendToBP, $sentAt );
    }

    function getObject( $payment )
    {
        return $this->_getObject( $payment, $this->factory, 'payment_id' );
    }

}
