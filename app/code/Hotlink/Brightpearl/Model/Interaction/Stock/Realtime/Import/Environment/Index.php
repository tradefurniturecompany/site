<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment;

class Index extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Boolean
{
    public function getDefault()
    {
        return false;
    }

    public function getKey()
    {
        return 'run_price_index';
    }

    public function getName()
    {
        return 'Run price index';
    }

    public function getNote()
    {
        return "Check only if SKUs participate in a bundle product.";
    }

    public function getValue()
    {
        if ( !$this->_valueInitialised ) {
            $this->setValue( $this->getDefault() );
        }
        return $this->_value;
    }
}