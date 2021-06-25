<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Lookup\Order\Status;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    function _construct()
    {
        $this->_init( 'Hotlink\Brightpearl\Model\Lookup\Order\Status', 'Hotlink\Brightpearl\Model\ResourceModel\Lookup\Order\Status' );
    }

}