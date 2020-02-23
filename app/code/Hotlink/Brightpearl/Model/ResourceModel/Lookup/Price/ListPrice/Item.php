<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Lookup\Price\ListPrice;

class Item extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    function _construct()
    {
        $this->_init( 'hotlink_brightpearl_lookup_price_list_item', 'id' );
    }

}