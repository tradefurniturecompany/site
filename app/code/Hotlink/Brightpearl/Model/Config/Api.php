<?php
namespace Hotlink\Brightpearl\Model\Config;

class Api extends \Hotlink\Brightpearl\Model\Config\AbstractConfig
{
    protected function _getGroup()
    {
        return 'api';
    }

    function getQueryLimit($storeId = null)
    {
        return $this->getConfigData( 'query_limit', $storeId, null );
    }
}