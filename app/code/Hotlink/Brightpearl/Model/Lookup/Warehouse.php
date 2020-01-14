<?php
namespace Hotlink\Brightpearl\Model\Lookup;

class Warehouse extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\ResourceModel\Lookup\Warehouse', 'id' );
    }
}