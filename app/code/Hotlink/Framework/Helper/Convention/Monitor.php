<?php
namespace Hotlink\Framework\Helper\Convention;

class Monitor extends \Hotlink\Framework\Helper\Convention\AbstractConvention
{

    function getConfig( \Hotlink\Framework\Model\Monitor\AbstractMonitor $monitor )
    {
        return $this->_getValidClass( $monitor, 'Config', false );
    }

}
