<?php
namespace Hotlink\Brightpearl\Model\Lookup\Order\Custom;

class Field extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\ResourceModel\Lookup\Order\Custom\Field' );
    }
}