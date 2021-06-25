<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Lookup\Order\Custom\Field;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    public function _construct()
    {
        $this->_init( 'Hotlink\Brightpearl\Model\Lookup\Order\Custom\Field', 'Hotlink\Brightpearl\Model\ResourceModel\Lookup\Order\Custom\Field' );
    }

}