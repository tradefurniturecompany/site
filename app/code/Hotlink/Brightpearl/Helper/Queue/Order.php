<?php
namespace Hotlink\Brightpearl\Helper\Queue;

class Order extends \Hotlink\Brightpearl\Helper\Queue\AbstractQueue
{

    protected $factory;

    public function __construct(
        \Hotlink\Brightpearl\Model\Queue\OrderFactory $factory
    )
    {
        $this->factory = $factory;
    }

    public function getObject( $order )
    {
        return $this->_getObject( $order, $this->factory, 'order_id' );
    }


    public function update( $object, $inBP, $sendToBP, $sentAt = null, $sentToken = null, $sentOAuth2InstanceId = null )
    {
        $object
            ->setInBp( $inBP )
            ->setSendToBp( $sendToBP );

        if ( $sentAt !== null ) {
            $object->setSentAt( $sentAt );
        }

        if ( $sentToken !== null ) {
            $object->setSentToken( $sentToken );
        }

        if ( $sentOAuth2InstanceId !== null ) {
            $object->setSentOauthInstanceId( $sentOAuth2InstanceId );
        }

        $object->save();
    }

}
