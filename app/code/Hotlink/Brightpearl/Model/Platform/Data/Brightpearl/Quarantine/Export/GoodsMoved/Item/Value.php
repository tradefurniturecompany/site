<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Quarantine\Export\GoodsMoved\Item;

class Value extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Creditmemo $creditmemo, $extra )
    {
        $helper = $this->getHelper();

        $bpSalesCredit    = $extra[ 'bpSalesCredit'    ];
        $bpProducts       = $extra[ 'bpProducts'       ];
        $bpPriceList      = $extra[ 'bpPriceList'      ];
        $bpSalesCreditRow = $extra[ 'bpSalesCreditRow' ];

        if ( $helper->getCurrencyCode( $creditmemo ) != $bpPriceList[ 'currencyCode' ] )
            {
                $this->throwValidationException
                    ( "Creditnote currency code " . $helper->getCurrencyCode( $creditmemo )
                      . "does not match price list currency code " . $bpPriceList[ 'currencyCode' ]
                    );
            }
        $this[ 'currency' ] = $helper->getCurrencyCode( $creditmemo );

        $productId = $bpSalesCreditRow->getData( 'productId' );
        $bpProduct = $bpProducts[ $productId ];

        $sku = $bpProduct[ 'identity' ][ 'sku' ];
        $prices = $bpPriceList[ 'prices' ][ $sku ];

        $this[ 'value' ] = $this->getPrice( $sku, $prices );
        return $this;
    }

    protected function getPrice( $sku, $prices )
    {
        if ( !is_array( $prices ) )
            {
                $this->exceptionHelper->throwProcessing( "No valid prices available for sku [$sku] to create quarantine note. Check the api call to retrieve prices returned actual data.", $this );
            }
        if ( count( $prices ) == 0 )
            {
                $this->exceptionHelper->throwProcessing( "No valid prices available for sku [$sku] to create quarantine note. Check the api call to retrieve prices returned actual data.", $this );
            }
        $qty = min( array_keys( $prices ) );
        return $prices[ $qty ] / $qty;
    }

}