<?php
namespace Hotlink\Framework\Model\Schedule;

class Config extends \Hotlink\Framework\Model\Config\Module\AbstractConfig
{

    protected function _getGroup()
    {
        return 'installation';
    }

    function getLoggingEnabled( $storeId = null )
    {
        return $this->getConfigData( 'enable_logging', $storeId, false );
    }

}