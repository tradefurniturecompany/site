<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Realtime\Import;

class Config extends \Hotlink\Brightpearl\Model\Interaction\Order\Status\Import\Config\AbstractConfig
{
    function getNotifyCustomer( $storeId = null )
    {
        return $this->getConfigData( 'notify_customer', $storeId );
    }
}
