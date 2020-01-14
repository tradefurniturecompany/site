<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Quarantine;

class Export extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Creditmemo $creditmemo, $extra )
    {
        try
            {
                $helper = $this->getHelper();

                $extra[ 'bpProducts' ] = $this->index( $extra[ 'bpProducts' ] );

                $bpSalesCredit = $extra[ 'bpSalesCredit' ];
                $bpProducts    = $extra[ 'bpProducts'    ];
                $bpPriceList   = $extra[ 'bpPriceList'   ];
                $warehouse     = $extra[ 'warehouse'     ];

                $this[ 'transfer'    ] = false;
                $this[ 'warehouseId' ] = $warehouse->getBrightpearlId();
                $this[ 'receivedOn'  ] = $helper->formatDate( $creditmemo->getCreatedAt() );
                $this[ 'goodsMoved'  ] = $this->getObject( $creditmemo, 'GoodsMoved', true, $extra );
            }
        catch ( \Exception $e )
            {
                $this->throwValidationException( "Unable to generate Quarantine Note data structure ---> " . $e->getMessage(), $e );
            }
    }

    protected function index( $items, $field = 'id' )
    {
        $indexed = [];
        foreach ( $items as $item )
            {
                $itemId = $item->getData( $field );
                $indexed[ $itemId ] = $item;
            }
        return $indexed;
    }

}