<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Stock;

class Item extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    function _construct()
    {
        $this->_init( 'hotlink_brightpearl_stock_item', 'id' );
    }

}