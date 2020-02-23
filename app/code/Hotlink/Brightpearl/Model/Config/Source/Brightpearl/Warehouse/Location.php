<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Warehouse;

class Location implements \Magento\Framework\Option\ArrayInterface
{

    const DEFAULT = 'default';
    const QUARANTINE = 'quarantine';

    protected $_options = false;

    /**
     * @var \Hotlink\Brightpearl\Model\Resource\Warehouse\CollectionFactory
     */
    protected $warehouseCollectionFactory;

    function __construct(
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Warehouse\CollectionFactory $warehouseCollectionFactory
    ) {
        $this->warehouseCollectionFactory = $warehouseCollectionFactory;
    }

    protected function _initOptions()
    {
        $options = [];
        $options[] =
                   [ 'value' => self::DEFAULT,
                     'label' => 'Product Default Location'
                   ];
        $options[] =
                   [ 'value' => self::QUARANTINE,
                     'label' => 'Warehouse Quarantine Location'
                   ];
        $this->_options = $options;
        return $this;
    }

    function toOptionArray()
    {
        if ( !$this->_options ) {
            $this->_initOptions();
        }
        return $this->_options;
    }

    function toArray()
    {
        if ( !$this->_options ) {
            $this->_initOptions();
        }

        $options = array();
        foreach($this->_options as $_opt){
            $options[ $_opt['value'] ] = $_opt['label'];
        }
        return $options;
    }

}
