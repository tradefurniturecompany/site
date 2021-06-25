<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Creditmemo\Export\Rows;

class Shipping extends \Hotlink\Brightpearl\Model\Platform\Data
{

    //
    // #262 The integration originally enforced BP data (since shipping costs may be adjusted within BP).
    //      This has been adjusted to use Magento UI numbers so that (logically) WYSIWYG applies.
    //
    protected function _map_object_magento( $brightpearlOrderItem, $extra )
    {
        $creditmemo  = $extra[ 'creditmemo'  ];
        $nominalCode = $extra[ 'nominalCode' ];
        $this[ "nominalCode" ] = $nominalCode;

        $this[ "productId" ] = $brightpearlOrderItem[ 'productId' ];
        $this[ "name"      ] = $brightpearlOrderItem[ 'productName' ];
        $this[ "quantity"  ] = $brightpearlOrderItem[ 'quantity' ][ 'magnitude' ];

        $this[ "taxCode"   ] = $brightpearlOrderItem[ 'rowValue' ][ 'taxCode' ];

        $helper = $this->getHelper();
        $total = $helper->getAmountByOrderCurrencyUsage( $creditmemo, 'shipping_incl_tax', 'base_shipping_incl_tax' );
        $tax   = $helper->getAmountByOrderCurrencyUsage( $creditmemo, 'shipping_tax_amount', 'base_shipping_tax_amount' );
        $this[ "net" ] = $total - $tax;
        $this[ "tax" ] = $tax;
        return $this;
    }

}