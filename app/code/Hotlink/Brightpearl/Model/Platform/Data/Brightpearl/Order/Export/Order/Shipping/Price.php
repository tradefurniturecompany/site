<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Shipping;

class Price extends \Hotlink\Brightpearl\Model\Platform\Data
{
    protected function _map_object_magento( \Magento\Sales\Model\Order $order )
    {
        $useBasePrice = ! $this->getHelper()->useOrderCurrency( $order );

        $amountExcludingTax = $this->getAmountExcludingTax( $order, $useBasePrice );
        $amountIncludingTax = $this->getAmountIncludingTax( $order, $useBasePrice );

        $this[ 'amountExcludingTax' ] = (float) $amountExcludingTax;
        $this[ 'amountIncludingTax' ] = (float) $amountIncludingTax;
        $this[ 'tax' ]                = (float) $amountIncludingTax - $amountExcludingTax;
    }

    protected function getAmountExcludingTax( \Magento\Sales\Model\Order $order, $base )
    {
        return ( $base )
            ? $order->getBaseShippingAmount()
            : $order->getShippingAmount();
    }

    protected function getAmountIncludingTax( \Magento\Sales\Model\Order $order, $base )
    {
        return ( $base )
            ? $order->getBaseShippingInclTax()
            : $order->getShippingInclTax();
    }
}