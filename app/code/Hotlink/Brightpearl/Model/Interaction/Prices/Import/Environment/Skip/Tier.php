<?php
namespace Hotlink\Brightpearl\Model\Interaction\Prices\Import\Environment\Skip;

class Tier extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Boolean
{
    public function getDefault()
    {
        return false;
    }

    public function getKey()
    {
        return 'skip_tier';
    }

    public function getName()
    {
        return 'Skip applying tier prices';
    }

    public function getNote()
    {
        return 'Do not apply tier prices during this interaction';
    }

    public function getValue()
    {
        if ( !$this->_valueInitialised ) {
            $env = $this->getEnvironment();
            $storeId = $env->getStoreId();
            $this->setValue( $env->getConfig()->getConfigData( $this->getKey(), $storeId, $this->getDefault() ) );
        }
        return $this->_value;
    }
}
