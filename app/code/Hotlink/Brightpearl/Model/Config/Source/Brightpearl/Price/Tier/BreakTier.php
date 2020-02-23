<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Tier;

class BreakTier implements \Magento\Framework\Option\ArrayInterface
{
    protected $_options = false;

    const INCLUDE_BREAK_1 = 1;
    const EXCLUDE_BREAK_1 = 0;

    protected function _initOptions()
    {
        $this->_options = array(
            [ 'value' => self::EXCLUDE_BREAK_1, 'label' => __('Exclude break 1') ],
            [ 'value' => self::INCLUDE_BREAK_1, 'label' => __('Include break 1') ] );

        return $this;
    }

    function toOptionArray()
    {
        if (!$this->_options) {
            $this->_initOptions();
        }
        return $this->_options;
    }

    function toArray()
    {
        if (!$this->_options) {
            $this->_initOptions();
        }

        $options = array();
        foreach($this->_options as $_opt){
            $options[ $_opt['value'] ] = $_opt['label'];
        }
        return $options;
    }
}