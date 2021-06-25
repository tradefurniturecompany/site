<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Creditmemo\Export\Rows;

class Item extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Creditmemo\Item $item, $brightpearlOrderItem )
    {
        $helper = $this->getHelper();

        $productId = $brightpearlOrderItem[ 'productId' ];
        $taxCode = $brightpearlOrderItem[ 'rowValue' ][ 'taxCode' ];

        $this[ "productId" ] = $productId;
        $this[ "name"      ] = $item->getName();

        $qty = $item->getQty();
        $qty = is_null( $qty ) ? null : ((double) $qty);  // should not be null (but in case)

        $this[ "quantity"  ] = $qty;

        $total = $this->getTotal( $item );
        $tax = $this->getTax( $item );
        $tax = is_null( $tax ) ? 0 : $tax;

        $this[ "taxCode"   ] = $taxCode;
        $this[ "net"       ] = $total - $tax;
        $this[ "tax"       ] = $tax;
        return $this;
    }

    protected function getTotal( $item )
    {
        //
        //   These calculation are lifted and blended (so as to match admin ui precisely) from:
        //
        //      Magento/Weee/Block/Item/Price/Renderer.php
        //
        //          function getBaseTotalAmount($item)
        //          function getTotalAmount($item)
        //
        //          these two function utilise a Weee helper
        //
        //   and
        //
        //      Magento/Sales/Block/Order/Item/Renderer/DefaultRenderer.php
        //
        //          function getTotalAmount($item)
        //
        //          this function utilises persisted Weee tax data
        //
        $total = 0.0;
        $use = $this->getHelper()->getUseCurrency();
        switch ( $use )
            {
                case \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::ORDER:
                    $total = $item->getRowTotal()
                           - $item->getDiscountAmount()
                           + $item->getTaxAmount()
                           + $item->getDiscountTaxCompensationAmount()
                           + $item->getWeeeTaxAppliedRowAmount();
                    break;
                case \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::BASE:
                    $total = $item->getBaseRowTotal()
                           - $item->getBaseDiscountAmount()
                           + $item->getBaseTaxAmount()
                           + $item->getBaseDiscountTaxCompensationAmount()
                           + $item->getWeeeTaxAppliedRowAmount();
                    break;
            }
        return $total;
    }

    protected function getTax( $item )
    {
        $use = $this->getHelper()->getUseCurrency();
        switch ( $use )
            {
                case \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::ORDER:
                    return $item->getTaxAmount();
                    break;
                case \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::BASE:
                    return $item->getBaseTaxAmount();
                    break;
            }
        return 0.0;
    }

}