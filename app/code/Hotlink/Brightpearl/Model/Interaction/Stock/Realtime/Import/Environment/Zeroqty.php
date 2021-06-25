<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment;

class Zeroqty extends \Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment\YesNo\AbstractYesNo
{

    function getDefault()
    {
        return 1;
    }

    function getKey()
    {
        return 'set_qty_zero_when_missing';
    }

    function getName()
    {
        return 'Zero qty on missing availability';
    }

    function getNote()
    {
        return "Select Yes to set qty to 0 when availability missing in Brightpearl response. Select No to reject the sku.";
    }

    function getValue()
    {
        if ( !$this->_valueInitialised )
            {
                $storeId = $this->getEnvironment()->getStoreId();
                $this->setValue( $this->getEnvironment()->getConfig()->getQtyZeroWhenMissing( $storeId ) );
            }
        return $this->_value;
    }

}