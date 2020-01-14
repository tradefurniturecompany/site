<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Rows;

class Bundle extends \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Rows\Simple
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Item $item )
    {
        parent::_map_object_magento( $item );
        $this[ 'children' ] = $this->getObject( $item, 'Children', true );
    }

}