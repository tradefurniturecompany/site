<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Status;

class Export extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order $order )
    {
        $this[] = $this->getObject( $order, 'Status', true );
    }

}