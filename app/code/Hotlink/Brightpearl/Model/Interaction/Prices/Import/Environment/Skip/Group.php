<?php
namespace Hotlink\Brightpearl\Model\Interaction\Prices\Import\Environment\Skip;

class Group extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Boolean
{
    function getDefault()
    {
        return false;
    }

    function getKey()
    {
        return 'skip_group';
    }

    function getName()
    {
        return 'Skip applying group prices';
    }

    function getNote()
    {
        return 'Do not apply customer group prices during this interaction';
    }

    function getValue()
    {
        if ( !$this->_valueInitialised ) {
            $env = $this->getEnvironment();
            $storeId = $env->getStoreId();
            $this->setValue($env->getConfig()->getConfigData($this->getKey(), $storeId, $this->getDefault()));
        }
        return $this->_value;
    }
}
