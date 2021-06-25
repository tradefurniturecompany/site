<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Warehouse;

class Optional extends \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Warehouse
{

    protected function _initOptions()
    {
        parent::_initOptions();
        $empty = [ 'value' => 0,
                   'label' => ' ' ];
        array_unshift( $this->_options, $empty );
        return $this;
    }

}
