<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order;

class Channel implements \Magento\Framework\Option\ArrayInterface
{
    protected $_data = null;

    /**
     * @var \Hotlink\Brightpearl\Model\Resource\Channel\CollectionFactory
     */
    protected $brightpearlResourceChannelCollectionFactory;

    function __construct(
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Channel\CollectionFactory $brightpearlResourceChannelCollectionFactory
    ) {
        $this->brightpearlResourceChannelCollectionFactory = $brightpearlResourceChannelCollectionFactory;
    }

    protected function _init()
    {
        if ( is_null( $this->_data ) )
            {
                $this->_data = array( array( 'value' => '', 'label' => ' ' ) );
                $channels = $this->brightpearlResourceChannelCollectionFactory->create()
                    ->addFieldToFilter( 'deleted', array( 'eq' => '0' ) );
                $channels->getSelect()->order( 'name ASC' );

                foreach ( $channels as $channel )
                    {
                        $this->_data[] = array( 'value' => $channel->getBrightpearlId(),
                                                'label' => '[' . $channel->getBrightpearlId() . '] ' . $channel->getName() );
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
