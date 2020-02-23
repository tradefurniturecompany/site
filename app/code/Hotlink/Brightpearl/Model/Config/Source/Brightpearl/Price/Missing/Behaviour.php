<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Missing;

class Behaviour implements \Magento\Framework\Option\ArrayInterface
{
    const BEHAVIOUR_UNCHANGED = 1;
    const BEHAVIOUR_CLEAR     = 2;

    protected $_options;

    protected function _initOptions()
    {
        $this->_options = [ [ 'value' => self::BEHAVIOUR_UNCHANGED, 'label' => 'Leave unchanged' ],
                            [ 'value' => self::BEHAVIOUR_CLEAR, 'label' => 'Clear value' ] ];
        return $this;
    }

    function toOptionArray()
    {
        if(!$this->_options)
            $this->_initOptions();
        return $this->_options;
    }

    function toArray()
    {
        if(!$this->_options)
            $this->_initOptions();

        $options = array();
        foreach($this->_options as $_opt){
            $options[ $_opt['value'] ] = $_opt['label'];
        }
        return $options;
    }
}
