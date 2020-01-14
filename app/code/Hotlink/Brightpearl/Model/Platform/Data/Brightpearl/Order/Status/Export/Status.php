<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Status\Export;

class Status extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order $order )
    {
        $helper = $this->getHelper();
        $this['op']    = 'replace';
        $this['path']  = '/status';
        $this['value'] = $helper->getOrderStatus( $order );
    }

}