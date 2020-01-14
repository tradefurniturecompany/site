<?php
namespace Hotlink\Brightpearl\Model\Lookup\Shipping;

class Method extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\ResourceModel\Lookup\Shipping\Method' );
    }
}