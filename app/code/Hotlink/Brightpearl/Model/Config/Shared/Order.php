<?php
namespace Hotlink\Brightpearl\Model\Config\Shared;

class Order extends \Hotlink\Brightpearl\Model\Config\AbstractConfig
{
    protected function _getGroup()
    {
        return 'shared_order';
    }

    public function getUseCurrency( $storeId=null )
    {
        return $this->getConfigData( 'use_currency', $storeId, \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::BASE );
    }

    public function getOrderStatusMap( $storeId = null )
    {
        return $this->getSerializedField( 'status_map', $storeId, [] );
    }

    public function getOrderStatusDefault( $storeId=null )
    {
        return $this->getConfigData( 'status_default', $storeId, null );
    }

    public function getPaymentMethodMap( $storeId=null )
    {
        return $this->getSerializedField( 'payment_map', $storeId, [] );
    }

    public function getPaymentMethodDefault( $storeId=null )
    {
        return $this->getConfigData( 'payment_default', $storeId, null );
    }

    public function getPaymentMethodDefaultCreateReceipts( $storeId=null )
    {
        return $this->getConfigData( 'payment_default_receipt', $storeId, null );
    }

}
