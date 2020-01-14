<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order;

class Notes extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order $order )
    {
        //$history = $order->getVisibleStatusHistory();
        $history = $order->getAllStatusHistory();
        $comment = '';
        foreach ( $history as $status )
            {
                $comment = $status->getComment();
                break;
            }
        $this[] = [ 'text' => $comment ];
    }

}
