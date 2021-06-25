<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment;

class Zeroqty extends \Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment\YesNo\AbstractYesNo
{

    public function getDefault()
    {
        return 1;
    }

    public function getKey()
    {
        return 'set_qty_zero_when_missing';
    }

    public function getName()
    {
        return 'Zero qty on missing availability';
    }

    public function getNote()
    {
        return "Select Yes to set qty to 0 when availability missing in Brightpearl response. Select No to reject the sku.";
    }

    public function getValue()
    {
        if ( !$this->_valueInitialised )
            {
                $storeId = $this->getEnvironment()->getStoreId();
                $this->setValue( $this->getEnvironment()->getConfig()->getQtyZeroWhenMissing( $storeId ) );
            }
        return $this->_value;
    }

}