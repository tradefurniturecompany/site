<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Shipping;

class Total extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order $order )
    {
        $useBasePrice = ! $this->getHelper()->useOrderCurrency( $order );
        if ( $useBasePrice )
            {
                $this[ 'amountExcludingTax' ] = $order->getBaseShippingAmount();
                $this[ 'amountIncludingTax' ] = $order->getBaseShippingInclTax();
                $this[ 'tax' ]                = $order->getBaseShippingTaxAmount();
            }
        else
            {
                $this[ 'amountExcludingTax' ] = $order->getShippingAmount();
                $this[ 'amountIncludingTax' ] = $order->getShippingInclTax();
                $this[ 'tax' ]                = $order->getShippingTaxAmount();
            }
    }

}
