<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order;

class Total extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order $order )
    {
        $useBasePrice = ! $this->getHelper()->useOrderCurrency( $order );

        $amountIncludingTax = ( $useBasePrice ) ? $order->getBaseGrandTotal() : $order->getGrandTotal();
        $taxAmount          = ( $useBasePrice ) ? $order->getBaseTaxAmount()  : $order->getTaxAmount();
        $amountExcludingTax = $amountIncludingTax - $taxAmount;

        $this[ 'amountExcludingTax' ] = (float) $amountExcludingTax;
        $this[ 'amountIncludingTax' ] = (float) $amountIncludingTax;
        $this[ 'tax'                ] = (float) $taxAmount;
    }

}