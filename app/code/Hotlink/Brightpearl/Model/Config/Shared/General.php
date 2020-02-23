<?php
namespace Hotlink\Brightpearl\Model\Config\Shared;

class General extends \Hotlink\Brightpearl\Model\Config\AbstractConfig
{
    protected function _getGroup()
    {
        return 'shared_general';
    }

    function getChannel( $storeId = null )
    {
        return $this->getConfigData( 'channel', $storeId );
    }
}
