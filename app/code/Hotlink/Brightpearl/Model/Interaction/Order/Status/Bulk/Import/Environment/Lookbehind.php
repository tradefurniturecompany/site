<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Bulk\Import\Environment;

class Lookbehind extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Scalar\Timespan
{
    public function getName()
    {
        return 'Lookbehind';
    }

    public function getKey()
    {
        return 'lookbehind';
    }

    public function getNote()
    {
        return 'Fetch orders modified in the past ...';
    }

    public function getDefaultUnit()
    {
        return 'hours';
    }

    public function getValue()
    {
        if ( !$this->_valueInitialised )
            {
                $storeId = $this->getEnvironment()->getStoreId();
                $this->setValue( $this->getEnvironment()->getConfig()->getConfigData( $this->getKey(), $storeId, $this->getDefault() ) );
            }
        return $this->_value;
    }
}
