<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Custom;

class Field implements \Magento\Framework\Option\ArrayInterface
{
    protected $_data = null;

    /**
     * @var \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Order\Custom\Field\CollectionFactory
     */
    protected $brightpearlResourceOrderFieldCollectionFactory;

    public function __construct(
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Order\Custom\Field\CollectionFactory $brightpearlResourceOrderFieldCollectionFactory
    ) {
        $this->brightpearlResourceOrderFieldCollectionFactory = $brightpearlResourceOrderFieldCollectionFactory;
    }

    protected function _init()
    {
        if ( is_null( $this->_data ) )
            {
                $this->_data = array( array( 'value' => '', 'label' => ' ' ) );
                $collection = $this->brightpearlResourceOrderFieldCollectionFactory->create()
                    ->addFieldToFilter( 'deleted', array( 'eq' => '0' ) );
                $collection->getSelect()->order( 'name ASC' );

                foreach ( $collection as $item )
                    {
                        $this->_data[] = array( 'value' => $item->getCode(),
                                                'label' => '[' . $item->getCode() . '] ' . $item->getName() );
                    }
            }
        return $this;
    }

    public function toOptionArray()
    {
        $this->_init();
        return $this->_data;
    }

    public function toArray()
    {
        $ret = array();
        foreach ( $this->toOptionArray() as $item )
            {
                $ret[ $item['value'] ] = $item['label'];
            }
        return $ret;
    }

}
