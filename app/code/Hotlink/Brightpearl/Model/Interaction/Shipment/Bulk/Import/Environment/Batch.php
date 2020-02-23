<?php
namespace Hotlink\Brightpearl\Model\Interaction\Shipment\Bulk\Import\Environment;

class Batch extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    function getDefault()
    {
        return 50;
    }

    function getName()
    {
        return 'Batch size';
    }

    function getNote()
    {
        return 'Noumber of records to fetch per Brightpearl query';
    }

    function getKey()
    {
        return 'batch';
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
