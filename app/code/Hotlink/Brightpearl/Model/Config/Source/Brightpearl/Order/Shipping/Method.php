<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Shipping;

class Method implements \Magento\Framework\Option\ArrayInterface
{
    protected $_data = null;

    /**
     * @var \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Shipping\Method\CollectionFactory
     */
    protected $brightpearlResourceShippingMethodCollectionFactory;

    function __construct(
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Shipping\Method\CollectionFactory $brightpearlResourceShippingMethodCollectionFactory
    ) {
        $this->brightpearlResourceShippingMethodCollectionFactory = $brightpearlResourceShippingMethodCollectionFactory;
    }

    protected function _init()
    {
        if ( is_null( $this->_data ) )
            {
                $this->_data = array( array( 'value' => '', 'label' => ' ' ) );
                $collection = $this->brightpearlResourceShippingMethodCollectionFactory->create()
                    ->addFieldToFilter( 'deleted', array( 'eq' => '0' ) );
                $collection->getSelect()->order( 'name ASC' );

                foreach ( $collection as $item )
                    {
                        $this->_data[] = array( 'value' => $item->getBrightpearlId(),
                                                'label' => '[' . $item->getBrightpearlId() . '] ' . $item->getCode() . ' - ' . $item->getName() );
                    }
            }
        return $this;
    }

    function toOptionArray()
    {
        $this->_init();
        return $this->_data;
    }

    function toArray()
    {
        $ret = array();
        foreach ( $this->toOptionArray() as $item )
            {
                $ret[ $item['value'] ] = $item['label'];
            }
        return $ret;
    }

}
