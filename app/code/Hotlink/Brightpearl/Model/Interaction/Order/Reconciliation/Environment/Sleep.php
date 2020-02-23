<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation\Environment;

class Sleep extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{
    function getDefault()
    {
        return 5000;
    }

    function getName()
    {
        return 'Sleep between saves';
    }

    function getNote()
    {
        return 'Millionths of a second (micro seconds)';
    }

    function getKey()
    {
        return 'sleep';
    }

    function getValue()
    {
        if ( !$this->_valueInitialised ) {
            $storeId = $this->getEnvironment()->getStoreId();
            $this->setValue( $this->getEnvironment()->getConfig()->getConfigData( $this->getKey(), $storeId, $this->getDefault() ) );
        }
        return $this->_value;
    }
}
