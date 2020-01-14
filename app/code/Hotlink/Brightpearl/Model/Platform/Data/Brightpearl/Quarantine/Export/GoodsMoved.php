<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Quarantine\Export;

class GoodsMoved extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Creditmemo $creditmemo, $extra )
    {
        $helper = $this->getHelper();

        $bpSalesCredit = $extra[ 'bpSalesCredit' ];
        $bpProducts    = $extra[ 'bpProducts'    ];
        $bpPriceList   = $extra[ 'bpPriceList'   ];
        $warehouse     = $extra[ 'warehouse'     ];

        $storeId = $creditmemo->getStoreId();
        $helper = $this->getHelper();
        $config = $helper->getConfig();
        $nominalCode = $config->getRefundsShippingNominalCode( $storeId );

        foreach ( $bpSalesCredit->getRows() as $row )
            {
                if ( ! $this->isShipping( $row, $nominalCode ) )
                    {
                        $extra[ 'bpSalesCreditRow' ] = $row;
                        $this[] = $this->getObject( $creditmemo, 'Item', true, $extra );
                        unset( $extra[ 'bpSalesCreditRow' ] );
                    }
            }
    }

    protected function isShipping( $item, $nominalCode )
    {
        return ( $item[ 'nominalCode' ] == $nominalCode );
    }

}
