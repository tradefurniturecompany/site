<?php
namespace Hotlink\Brightpearl\Model\Interaction\Prices\Import\Environment\Skip;

class Attribute extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Boolean
{
    public function getDefault()
    {
        return false;
    }

    public function getKey()
    {
        return 'skip_attributes';
    }

    public function getName()
    {
        return 'Skip applying attributes';
    }

    public function getNote()
    {
        return 'Do not apply product mapping during this interaction';
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
