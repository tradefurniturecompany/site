<?php
namespace Hotlink\Framework\Model\Interaction\Environment\Parameter\Config;

abstract class AbstractConfig extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    //
    //  Overload to specialise parameter values
    //
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
