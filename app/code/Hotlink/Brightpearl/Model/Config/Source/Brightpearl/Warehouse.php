<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl;

class Warehouse implements \Magento\Framework\Option\ArrayInterface
{
    protected $_options = false;

    /**
     * @var \Hotlink\Brightpearl\Model\Resource\Warehouse\CollectionFactory
     */
    protected $warehouseCollectionFactory;

    public function __construct(
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Warehouse\CollectionFactory $warehouseCollectionFactory
    ) {
        $this->warehouseCollectionFactory = $warehouseCollectionFactory;
    }

    protected function _initOptions()
    {
        $options = array();
        $collection = $this->warehouseCollectionFactory->create();
        foreach ($collection as $item) {
            $data = [
                'value' => $item->getData('id'),
                'label' => $item->getData('name')
            ];
            if ($item->getDeleted() == 1) {
                $data['label'] .= " (Deleted)";
            }
            $options[] = $data;
        }

        $this->_options = $options;
        return $this;
    }

    public function toOptionArray()
    {
        if ( !$this->_options ) {
            $this->_initOptions();
        }
        return $this->_options;
    }

    public function toArray()
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
