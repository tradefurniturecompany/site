<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Lookup\Channel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\Lookup\Channel', '\Hotlink\Brightpearl\Model\ResourceModel\Lookup\Channel');
    }

}