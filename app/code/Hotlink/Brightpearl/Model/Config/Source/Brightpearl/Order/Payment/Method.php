<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Payment;

class Method implements \Magento\Framework\Option\ArrayInterface
{
    protected $_data = null;

    /**
     * @var \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Nominal\Code\CollectionFactory
     */
    protected $brightpearlResourceNominalCodeCollectionFactory;

    function __construct(
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Nominal\Code\CollectionFactory $brightpearlResourceNominalCodeCollectionFactory
    ) {
        $this->brightpearlResourceNominalCodeCollectionFactory = $brightpearlResourceNominalCodeCollectionFactory;
    }

    protected function _init()
    {
        if ( is_null( $this->_data ) )
            {
                $this->_data = array( array( 'value' => '', 'label' => ' ' ) );
                $collection = $this->brightpearlResourceNominalCodeCollectionFactory->create()
                    ->addFieldToFilter( 'deleted', array( 'eq' => '0' ) );
                $collection->getSelect()->order( 'code ASC' );

                foreach ( $collection as $item )
                    {
                        $this->_data[] = array( 'value' => $item->getCode(),
                                                'label' => $item->getCode() . ' - ' . $item->getName() . ' (#' . $item->getBrightpearlId() . ')' );
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
