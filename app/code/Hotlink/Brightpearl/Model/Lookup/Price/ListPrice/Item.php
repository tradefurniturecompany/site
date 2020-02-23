<?php
namespace Hotlink\Brightpearl\Model\Lookup\Price\ListPrice;

class Item extends \Magento\Framework\Model\AbstractModel
{
    function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\ResourceModel\Lookup\Price\ListPrice\Item' );
    }
}