<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Shipment;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\Shipment', '\Hotlink\Brightpearl\Model\ResourceModel\Shipment');
    }
}
