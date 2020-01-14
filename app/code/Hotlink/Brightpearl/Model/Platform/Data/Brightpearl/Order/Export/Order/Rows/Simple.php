<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Rows;

class Simple extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Item $item )
    {
        $helper = $this->getHelper();

        $qty = $item->getQtyOrdered();
        $qty = is_null( $qty ) ? null : ((double) $qty);

        $this[ 'quantity'       ] = $qty;
        $this[ 'sku'            ] = $helper->extractOrderItemOriginalSku( $item );
        $this[ 'name'           ] = $item->getName();
        $this[ 'externalRef'    ] = $item->getId();
        $this[ 'productOptions' ] = $opts = $helper->extractOrderItemProductOptions( $item );

        if ( is_array( $opts ) )
            {
                // adjust sku and name if modified by custom options (Hotlink_Interaction stores original values)
                if ( array_key_exists( 'hotlink_original_sku', $opts ) )
                    {
                        $this['sku'] = $opts['hotlink_original_sku'];
                    }
                if ( array_key_exists( 'hotlink_original_name', $opts ) )
                    {
                        $this['name'] = $opts['hotlink_original_name'];
                    }
            }

        $this[ 'price'    ] = $this->getObject( $item, 'Price', true );
        $this[ 'total'    ] = $this->getObject( $item, 'Total', true );
        $this[ 'children' ] = array();
    }

}
