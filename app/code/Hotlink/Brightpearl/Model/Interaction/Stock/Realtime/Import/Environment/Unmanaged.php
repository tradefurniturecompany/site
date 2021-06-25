<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment;

class Unmanaged extends \Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment\YesNo\AbstractYesNo
{

    public function getDefault()
    {
        return 1;
    }

    public function getKey()
    {
        return 'skip_unmanaged';
    }

    public function getName()
    {
        return 'Skip unmanaged';
    }

    public function getNote()
    {
        return "Select Yes to skip importing stock availability for products with 'Manage Stock' No or,
select No to import stock availability even when 'Manage Stock' is No.";
    }

    public function getValue()
    {
        if ( !$this->_valueInitialised )
            {
                $storeId = $this->getEnvironment()->getStoreId();
                $this->setValue( $this->getEnvironment()->getConfig()->getSkipUnmanaged( $storeId ) );
            }
        return $this->_value;
    }

}