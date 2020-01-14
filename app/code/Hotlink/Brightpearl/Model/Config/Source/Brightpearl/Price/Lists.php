<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price;

class Lists implements \Magento\Framework\Option\ArrayInterface
{
    protected $_options = false;

    const ID            = 'id';
    const CODE          = 'code';
    const PRICES        = 'prices';
    const CURRENCY_CODE = 'currencyCode';
    const GROSS         = 'gross';

    protected $priceListItemCollectionFactory;

    public function __construct(
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Price\ListPrice\Item\CollectionFactory $priceListItemCollectionFactory
    ) {
        $this->priceListItemCollectionFactory = $priceListItemCollectionFactory;
    }

    protected function _initOptions()
    {
        $options = array();
        $collection = $this->getCollection();
        foreach ($collection as $item) {
            $data = array(
                'value' => $item->getData('brightpearl_id'),
                'label' => $item->getData('name').' ['.$item->getData('currency_code').']'
            );
            if($item->getDeleted() == 1)
                $data['label'] .= " (Deleted)";

            $options[] = $data;
        }

        $this->_options = $options;
        return $this;
    }

    protected function getCollection()
    {
        return $this->priceListItemCollectionFactory->create();
    }

    public function toOptionArray()
    {
        if(!$this->_options)
            $this->_initOptions();
        return $this->_options;
    }

    public function toArray()
    {
        if(!$this->_options)
            $this->_initOptions();

        $options = array();
        foreach($this->_options as $_opt){
            $options[ $_opt['value'] ] = $_opt['label'];
        }
        return $options;
    }

    public function getName( $id )
    {
        foreach ( $this->toArray() as $key => $name )
            {
                if ( $key == $id )
                    {
                        return $name;
                    }
            }
        return "($id not matched)";
    }

}