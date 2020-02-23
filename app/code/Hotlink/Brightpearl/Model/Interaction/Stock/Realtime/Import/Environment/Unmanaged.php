<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment;

class Unmanaged extends \Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment\YesNo\AbstractYesNo
{

    function getDefault()
    {
        return 1;
    }

    function getKey()
    {
        return 'skip_unmanaged';
    }

    function getName()
    {
        return 'Skip unmanaged';
    }

    function getNote()
    {
        return "Select Yes to skip importing stock availability for products with 'Manage Stock' No or,
select No to import stock availability even when 'Manage Stock' is No.";
    }

    function getValue()
    {
        if ( !$this->_valueInitialised )
            {
                $storeId = $this->getEnvironment()->getStoreId();
                $this->setValue( $this->getEnvironment()->getConfig()->getSkipUnmanaged( $storeId ) );
            }
        return $this->_value;
    }

}