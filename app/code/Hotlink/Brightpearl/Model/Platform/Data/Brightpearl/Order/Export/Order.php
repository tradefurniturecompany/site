<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export;

class Order extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order $order )
    {
        $helper = $this->getHelper();

        $this[ 'externalRef'     ] = $helper->getOrderExternalReference( $order );
        $this[ 'channelId'       ] = $helper->getOrderChannelId();
        $this[ 'status'          ] = $helper->getOrderStatus( $order );
        $this[ 'createdAt'       ] = $helper->getOrderCreatedAt( $order );
        $this[ 'currencyCode'    ] = $helper->getOrderCurrencyCode( $order );
        $this[ 'giftCardsAmount' ] = $helper->getOrderGiftCardsAmount( $order );

        $this[ 'notes'           ] = $this->getObject( $order, 'Notes', true );
        $this[ 'customFields' ]    = $this->getObject( $order, 'CustomFields', true );

        if ( $helper->hasShippingMethod( $order ) )
            {
                $this[ 'shipping' ] = $this->getObject( $order, 'Shipping', true );
            }

        $this[ 'total' ]           = $this->getObject( $order, 'Total', true );
        $this[ 'rows' ]            = $this->getObject( $order, 'Rows', true );
        if ( $warehouseId = $helper->getDefaultAllocationWarehouse() )
            {
                $this[ 'warehouseId' ] = $warehouseId;
            }
    }

    public function toArray( array $arrAttributes = array() )
    {
        $ret = parent::toArray( $arrAttributes );
        if ( array_key_exists( 'customFields', $ret ) && empty( $ret[ 'customFields' ] ) )
            {
                unset( $ret[ 'customFields' ] );
            }
        return $ret;
    }

}