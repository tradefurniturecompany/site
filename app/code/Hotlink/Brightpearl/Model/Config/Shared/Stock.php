<?php
namespace Hotlink\Brightpearl\Model\Config\Shared;

class Stock extends \Hotlink\Brightpearl\Model\Config\AbstractConfig
{
    protected function _getGroup()
    {
        return 'shared_stock';
    }


    function getWarehouses($storeId = null)
    {
        // Issue with explode !
        // it returns array with one empty item for both cases:
        //    - null
        //    - string that does not contain the splitter character

        $value = $this->getConfigData( 'warehouse', $storeId);
        if (is_string($value) && strpos($value, ",") !== false) {
            return explode(",", $value);
        }

        return $value;
    }

    function getStockSkipUnmanaged($storeId = null)
    {
        return $this->getConfigData( 'skip_unmanaged', $storeId);
    }

    function getPutBackInstockFlag($storeId = null)
    {
        return $this->getConfigData( 'put_back_instock', $storeId);
    }

    function getQtyZeroWhenMissingFlag($storeId = null)
    {
        return $this->getConfigData( 'set_qty_zero_when_missing', $storeId);
    }
}
