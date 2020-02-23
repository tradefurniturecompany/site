<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\Order\Shipment\Extra;

class Info extends \Magento\Shipping\Block\Adminhtml\View\Items
{

    function getBundleItems()
    {
        $orderItem = $this->getParentBlock()->getItem();
        $result = [];
        foreach ( $this->getShipment()->getAllItems() as $shipmentItem )
            {
                if ( $shipmentItem->getOrderItemId() == $orderItem->getId() )
                    {
                        try
                            {
                                $result = unserialize( $shipmentItem->getAdditionalData() );
                                $result = $result[ 'Brightpearl' ][ 'BundleItems' ];
                            }
                        catch ( Exception $e )
                            {
                            }
                        if ( ! $result )
                            {
                                $result = [];
                            }
                        return $result;
                    }
            }
        return $result;
    }

}
