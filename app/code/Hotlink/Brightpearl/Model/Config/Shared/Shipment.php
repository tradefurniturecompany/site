<?php
namespace Hotlink\Brightpearl\Model\Config\Shared;

class Shipment extends \Hotlink\Brightpearl\Model\Config\AbstractConfig
{
    protected function _getGroup()
    {
        return 'shared_shipment';
    }

    function getNotifyCustomer( $storeId = null )
    {
        return $this->getConfigData( 'notify_customer', $storeId );
    }

    function getShippingOptionsMap( $storeId = null )
    {
        return $this->getSerializedField( 'shipping_map', $storeId );
    }
}