<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order;

class Shipping extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order $order )
    {
        $helper = $this->getHelper();
        $this[ 'shippingMethodId' ] = $helper->getOrderShippingMethodId( $order );
        $this[ 'price'            ] = $this->getObject( $order, 'Price', true );
        $this[ 'total'            ] = $this->getObject( $order, 'Total', true );
        $this[ 'description'      ] = $order->getShippingDescription();
    }

}