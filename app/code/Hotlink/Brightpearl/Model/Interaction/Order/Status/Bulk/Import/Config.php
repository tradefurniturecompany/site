<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Bulk\Import;

class Config extends \Hotlink\Brightpearl\Model\Interaction\Order\Status\Import\Config\AbstractConfig
{
    function getSortBy( $storeId = null )
    {
        return $this->getConfigData( 'sort_by', $storeId );
    }

    function getSortDirection( $storeId = null )
    {
        return $this->getConfigData( 'sort_direction', $storeId );
    }
}
