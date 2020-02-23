<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment;

class Index extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Boolean
{
    function getDefault()
    {
        return false;
    }

    function getKey()
    {
        return 'run_price_index';
    }

    function getName()
    {
        return 'Run price index';
    }

    function getNote()
    {
        return "Check only if SKUs participate in a bundle product.";
    }

    function getValue()
    {
        if ( !$this->_valueInitialised ) {
            $this->setValue( $this->getDefault() );
        }
        return $this->_value;
    }
}