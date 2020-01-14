<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Creditmemo\Export;

class Rows extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Creditmemo $creditmemo, $brightpearlOrder )
    {
        $items = $creditmemo->getItems();
        foreach ( $items as $item )
            {
                $orderItemId = $item->getOrderItemId();
                if ( $brightpearlOrderItem = $this->getBrightpearlOrderItem( $brightpearlOrder, $orderItemId ) )
                    {
                        $this[] = $this->getObject( $item, "Item", true, $brightpearlOrderItem );
                    }
            }


        if ( $creditmemo->getShippingAmount() > 0 )
            {
                $storeId = $creditmemo->getStoreId();
                $helper = $this->getHelper();
                $config = $helper->getConfig();
                $nominalCode = $config->getRefundsShippingNominalCode( $storeId );

                $shippingItems = $this->getShippingItems( $brightpearlOrder, $nominalCode );
                if ( count( $shippingItems ) > 0 )
                    {
                        $shippingAmount = 0;
                        foreach ( $shippingItems as $shippingItem )
                            {
                                if ( isset( $shippingItem[ 'rowValue' ][ 'rowNet' ][ 'value' ] ) )
                                    {
                                        $amount = $shippingItem[ 'rowValue' ][ 'rowNet' ][ 'value' ];
                                        $shippingAmount = $shippingAmount + $amount;
                                    }
                            }
                        $extra = [];
                        $extra[ 'creditmemo'  ] = $creditmemo;
                        $extra[ 'nominalCode' ] = $nominalCode;
                        $this[] = $this->getObject( $shippingItem, "Shipping", true, $extra );
                    }
            }
        return $this;
    }

    protected function getBrightpearlOrderItem( $brightpearlOrder, $magentoOrderItemId )
    {
        foreach ( $brightpearlOrder[ 'orderRows' ] as $row )
            {
                if ( isset( $row[ 'externalRef' ] ) )
                    {
                        if ( $magentoOrderItemId == $row[ 'externalRef' ] )
                            {
                                return $row;
                            }
                    }
            }
        return false;
    }

    protected function getShippingItems( $brightpearlOrder, $nominalCode )
    {
        $rows = [];
        foreach ( $brightpearlOrder[ 'orderRows' ] as $id => $row )
            {
                if ( isset( $row[ 'nominalCode' ] ) )
                    {
                        if ( $row[ 'nominalCode' ] == $nominalCode )
                            {
                                $rows[ $id ] = $row;
                            }
                    }
            }
        return $rows;
    }

}