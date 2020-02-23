<?php
namespace Hotlink\Brightpearl\Model\Interaction\Prices\Import\Environment\Skip;

class Attribute extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Boolean
{
    function getDefault()
    {
        return false;
    }

    function getKey()
    {
        return 'skip_attributes';
    }

    function getName()
    {
        return 'Skip applying attributes';
    }

    function getNote()
    {
        return 'Do not apply product mapping during this interaction';
    }

    function getValue()
    {
        if ( !$this->_valueInitialised ) {
            $env = $this->getEnvironment();
            $storeId = $env->getStoreId();
            $this->setValue( $env->getConfig()->getConfigData( $this->getKey(), $storeId, $this->getDefault() ) );
        }
        return $this->_value;
    }
}
