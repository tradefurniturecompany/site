<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Creditmemo;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    protected $_data = null;

    /**
     * @var \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Order\Status\CollectionFactory
     */
    protected $brightpearlResourceOrderStatusCollectionFactory;

    function __construct(
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Order\Status\CollectionFactory $brightpearlResourceOrderStatusCollectionFactory
    ) {
        $this->brightpearlResourceOrderStatusCollectionFactory = $brightpearlResourceOrderStatusCollectionFactory;
    }

    protected function _init()
    {
        if ( is_null( $this->_data ) )
            {
                $this->_data = array( array( 'value' => '', 'label' => ' ' ) );
                $statuses = $this->brightpearlResourceOrderStatusCollectionFactory->create()
                    ->addFieldToFilter( 'order_type_code', 'SC' )
                    ->addFieldToFilter( 'deleted', array( 'eq' => '0' ) );
                $statuses->getSelect()->order( 'name ASC' );

                foreach ( $statuses as $status )
                    {
                        $this->_data[] = array( 'value' => $status->getBrightpearlId(),
                                                'label' => '[' . $status->getBrightpearlId() . '] ' . $status->getName() );
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
