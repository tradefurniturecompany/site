<?php
namespace Hotlink\Brightpearl\Model\Config;

class Webhook extends \Hotlink\Brightpearl\Model\Config\AbstractConfig
{
    protected function _getGroup()
    {
        return 'webhook';
    }

    function saveCallbackKey( $key, $storeId = null )
    {
        return $this->saveValue( $key, 'key', $storeId );
    }

    function getCallBackKey( $storeId = null )
    {
        return $this->getConfigData( 'key', $storeId, null );
    }
}