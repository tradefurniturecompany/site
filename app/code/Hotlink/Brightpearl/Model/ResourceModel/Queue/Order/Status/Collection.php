<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Queue\Order\Status;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    public function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\Queue\Order\Status', '\Hotlink\Brightpearl\ResourceModel\Queue\Order\Status' );
    }

}