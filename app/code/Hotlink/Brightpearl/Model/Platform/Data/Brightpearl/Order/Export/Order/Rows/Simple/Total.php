<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Rows\Simple;

class Total extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Item $item )
    {
        $useBasePrice = ! $this->getHelper()->useOrderCurrency( $item->getOrder() );

        $renderer = $this->getHelper()->getPriceRenderer();

        $totalInclTax = ( $useBasePrice ) ? $renderer->getBaseTotalAmount( $item ) : $renderer->getTotalAmount( $item );
        $totalTax     = ( $useBasePrice ) ? $item->getBaseTaxAmount()              : $item->getTaxAmount();
        $totalExclTax = $totalInclTax - $totalTax;

        $this[ 'amountExcludingTax' ] = (float) $totalExclTax;
        $this[ 'amountIncludingTax' ] = (float) $totalInclTax;
        $this[ 'tax' ]                = (float) $totalTax;
    }

}