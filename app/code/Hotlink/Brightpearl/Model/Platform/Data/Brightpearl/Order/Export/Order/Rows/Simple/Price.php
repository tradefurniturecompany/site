<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Rows\Simple;

class Price extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Item $item )
    {
        $useBasePrice = ! $this->getHelper()->useOrderCurrency( $item->getOrder() );

        $amountExcludingTax = $this->getAmountExcludingTax( $item, $useBasePrice );
        $amountIncludingTax = $this->getAmountIncludingTax( $item, $useBasePrice );

        $this[ 'amountExcludingTax' ] = (float) $amountExcludingTax;
        $this[ 'amountIncludingTax' ] = (float) $amountIncludingTax;
        $this[ 'tax' ]                = (float) $amountIncludingTax - $amountExcludingTax;
    }

    protected function getAmountExcludingTax( \Magento\Sales\Model\Order\Item $item, $base )
    {
        return ( $base )
            ? $item->getBasePrice()
            : $item->getPrice();
    }

    protected function getAmountIncludingTax( \Magento\Sales\Model\Order\Item $item, $base )
    {
        return ( $base )
            ? $item->getBasePriceInclTax()
            : $item->getPriceInclTax();
    }
}