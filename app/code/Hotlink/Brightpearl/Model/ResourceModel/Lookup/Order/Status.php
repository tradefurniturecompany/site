<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Lookup\Order;

class Status extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    function _construct()
    {
        $this->_init( 'hotlink_brightpearl_lookup_order_status', 'id');
    }

}