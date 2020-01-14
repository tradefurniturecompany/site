<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Rows;

class Giftcard extends \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Rows\Simple
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Item $item )
    {
        parent::_map_object_magento( $item );
        $this[ 'rowType' ] = 'giftcard';
    }

}