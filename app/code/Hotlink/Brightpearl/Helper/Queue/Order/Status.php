<?php
namespace Hotlink\Brightpearl\Helper\Queue\Order;

class Status extends \Hotlink\Brightpearl\Helper\Queue\AbstractQueue
{

    protected $factory;

    public function __construct(
        \Hotlink\Brightpearl\Model\Queue\Order\StatusFactory $factory
    )
    {
        $this->factory = $factory;
    }

    public function getObject( $order )
    {
        return $this->_getObject( $order, $this->factory, 'order_id' );
    }

}
