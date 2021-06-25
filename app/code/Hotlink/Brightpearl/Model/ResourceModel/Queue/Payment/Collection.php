<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Queue\Payment;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    public function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\Queue\Payment', '\Hotlink\Brightpearl\Model\ResourceModel\Queue\Payment' );
    }

}