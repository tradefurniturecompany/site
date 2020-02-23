<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Queue\Order;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\Queue\Order', '\Hotlink\Brightpearl\Model\ResourceModel\Queue\Order' );
    }

}