<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Quarantine\Export\GoodsMoved;

class Item extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Creditmemo $creditmemo, $extra )
    {
        $helper = $this->getHelper();

        $bpSalesCredit    = $extra[ 'bpSalesCredit'    ];
        $bpProducts       = $extra[ 'bpProducts'       ];
        $bpPriceList      = $extra[ 'bpPriceList'      ];
        $bpSalesCreditRow = $extra[ 'bpSalesCreditRow' ];

        $warehouse        = $extra[ 'warehouse'        ];
        $bpWarehouseId    = $warehouse->getBrightpearlId();

        $productId = $bpSalesCreditRow->getData( 'productId' );
        $bpProduct = $bpProducts[ $productId ];

        $destinationLocationId = null;
        switch ( $helper->getQuarantineWarehouseLocation() )
            {
                case \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Warehouse\Location::DEFAULT:
                    $destinationLocationId = $this->getWarehouseLocationDefaultId( $bpProduct, $bpWarehouseId );
                    break;
                case \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Warehouse\Location::QUARANTINE:
                    $destinationLocationId = $this->getWarehouseLocationQuarantineId( $warehouse );
                    break;
                default:
                    $destinationLocationId = $this->getWarehouseLocationDefaultId( $bpProduct, $bpWarehouseId );
                    break;
            }

        $this[ 'productId'             ] = $productId;
        $this[ 'purchaseOrderRowId'    ] = $bpSalesCreditRow->getId();
        $this[ 'quantity'              ] = $bpSalesCreditRow->getQuantity();
        $this[ 'destinationLocationId' ] = $destinationLocationId;
        $this[ 'productValue'          ] = $this->getObject( $creditmemo, 'Value', true, $extra );

        return $this;
    }

    protected function getWarehouseLocationDefaultId( $bpProduct, $bpWarehouseId )
    {
        if ( isset( $bpProduct[ 'warehouses' ][ $bpWarehouseId ][ 'defaultLocationId' ] ) )
            {
                return $bpProduct[ 'warehouses' ][ $bpWarehouseId ][ 'defaultLocationId' ];
            }
        return null;
    }

    protected function getWarehouseLocationQuarantineId( $warehouse )
    {
        return $warehouse->getQuarantineLocationId();
    }

}