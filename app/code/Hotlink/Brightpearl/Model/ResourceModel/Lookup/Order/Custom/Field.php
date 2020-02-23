<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Lookup\Order\Custom;

class Field extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    function _construct()
    {
        $this->_init( 'hotlink_brightpearl_lookup_order_custom_field', 'id');
    }

}