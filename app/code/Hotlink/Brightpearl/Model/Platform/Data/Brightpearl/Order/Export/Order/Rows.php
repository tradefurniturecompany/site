<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order;

class Rows extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order $order )
    {
        $items = $order->getAllVisibleItems();
        foreach ( $items as $item )
            {
                $type = $item->getProductType();
                $this[] = $this->getObject( $item, ucfirst($type), true );
            }
        return $this;
    }

    public function getChildClassDefault( $key )
    {
        $this->getReport()->warn( $this->annotate( "The product type $key is not explicitly defined, using Simple" ) );
        return '\Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Rows\Simple';
    }

}