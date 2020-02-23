<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Magento\Product;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    protected $_options;

    protected $productType;

    function __construct(
        \Magento\Catalog\Model\Product\Type $productType
        ) {
        $this->productType = $productType;
    }

    protected function _initOptions()
    {
        $this->_options = $this->productType->getOptions();
        return $this;
    }

    function toOptionArray()
    {
        if ( !$this->_options) {
            $this->_initOptions();
        }
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
