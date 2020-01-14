<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Rows;

class Configurable extends \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Rows\Simple
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Item $item )
    {
        parent::_map_object_magento( $item );

        $opts = $item->getProductOptions();
        if ( is_array( $opts ) )
            {
                if ( array_key_exists( 'simple_sku', $opts ) && ''!==$opts['simple_sku'] )
                    {
                        $this['sku'] = $opts['simple_sku'];
                    }
                if ( array_key_exists( 'simple_name', $opts ) && ''!==$opts['simple_name'] )
                    {
                        $this['name'] = $opts['simple_name'];
                    }
            }
    }

}