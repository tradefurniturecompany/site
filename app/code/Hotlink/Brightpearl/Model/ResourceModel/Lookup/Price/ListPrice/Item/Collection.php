<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Lookup\Price\ListPrice\Item;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    public function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\Lookup\Price\ListPrice\Item', '\Hotlink\Brightpearl\Model\ResourceModel\Lookup\Price\ListPrice\Item' );
    }

}