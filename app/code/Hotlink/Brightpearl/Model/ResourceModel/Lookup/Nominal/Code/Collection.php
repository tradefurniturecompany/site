<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Lookup\Nominal\Code;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    public function _construct()
    {
        $this->_init( 'Hotlink\Brightpearl\Model\Lookup\Nominal\Code', 'Hotlink\Brightpearl\Model\ResourceModel\Lookup\Nominal\Code' );
    }

}