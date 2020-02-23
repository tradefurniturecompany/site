<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Lookup\Shipping;

class Method extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    function _construct()
    {
        $this->_init( 'hotlink_brightpearl_lookup_shipping_method', 'id');
    }

}