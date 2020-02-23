<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Lookup\Shipping\Method;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\Lookup\Shipping\Method', '\Hotlink\Brightpearl\Model\ResourceModel\Lookup\Shipping\Method' );
    }

}