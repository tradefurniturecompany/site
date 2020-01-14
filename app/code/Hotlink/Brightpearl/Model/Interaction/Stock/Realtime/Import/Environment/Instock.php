<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment;

class Instock extends \Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment\YesNo\AbstractYesNo
{

    public function getDefault()
    {
        return 1;
    }

    public function getKey()
    {
        return 'put_back_instock';
    }

    public function getName()
    {
        return 'Back in stock';
    }

    public function getNote()
    {
        return "Select Yes to set product back in stock when availability greater than out of stock threshold. Select No to leave product out of stock. Qty is updated in both cases.";
    }

    public function getValue()
    {
        if ( !$this->_valueInitialised )
            {
                $storeId = $this->getEnvironment()->getStoreId();
                $this->setValue( $this->getEnvironment()->getConfig()->getPutBackInstock( $storeId ) );
            }
        return $this->_value;
    }

}