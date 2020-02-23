<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Bulk\Import\Environment;

class Lookbehind extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Scalar\Timespan
{
    function getName()
    {
        return 'Lookbehind';
    }

    function getKey()
    {
        return 'lookbehind';
    }

    function getNote()
    {
        return 'Fetch orders modified in the past ...';
    }

    function getDefaultUnit()
    {
        return 'hours';
    }

    function getValue()
    {
        if ( !$this->_valueInitialised )
            {
                $storeId = $this->getEnvironment()->getStoreId();
                $this->setValue( $this->getEnvironment()->getConfig()->getConfigData( $this->getKey(), $storeId, $this->getDefault() ) );
            }
        return $this->_value;
    }
}
