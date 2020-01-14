<?php
namespace Hotlink\Brightpearl\Model\Config;

class Webhook extends \Hotlink\Brightpearl\Model\Config\AbstractConfig
{
    protected function _getGroup()
    {
        return 'webhook';
    }

    public function saveCallbackKey( $key, $storeId = null )
    {
        return $this->saveValue( $key, 'key', $storeId );
    }

    public function getCallBackKey( $storeId = null )
    {
        return $this->getConfigData( 'key', $storeId, null );
    }
}