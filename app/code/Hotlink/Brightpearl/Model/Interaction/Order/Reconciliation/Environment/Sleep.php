<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation\Environment;

class Sleep extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{
    public function getDefault()
    {
        return 5000;
    }

    public function getName()
    {
        return 'Sleep between saves';
    }

    public function getNote()
    {
        return 'Millionths of a second (micro seconds)';
    }

    public function getKey()
    {
        return 'sleep';
    }

    public function getValue()
    {
        if ( !$this->_valueInitialised ) {
            $storeId = $this->getEnvironment()->getStoreId();
            $this->setValue( $this->getEnvironment()->getConfig()->getConfigData( $this->getKey(), $storeId, $this->getDefault() ) );
        }
        return $this->_value;
    }
}
