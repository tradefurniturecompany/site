<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order;

class Export extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order $order )
    {
        $this[ 'customer' ] = $this->getObject( $order, 'Customer', true );
        $this[ 'order' ] = $this->getObject( $order, 'Order', true );
    }

}