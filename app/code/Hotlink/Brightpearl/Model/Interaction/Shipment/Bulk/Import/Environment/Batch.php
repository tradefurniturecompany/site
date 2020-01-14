<?php
namespace Hotlink\Brightpearl\Model\Interaction\Shipment\Bulk\Import\Environment;

class Batch extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    public function getDefault()
    {
        return 50;
    }

    public function getName()
    {
        return 'Batch size';
    }

    public function getNote()
    {
        return 'Noumber of records to fetch per Brightpearl query';
    }

    public function getKey()
    {
        return 'batch';
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
