<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Lookup;

class Channel extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function _construct()
    {
        $this->_init( 'hotlink_brightpearl_lookup_channel', 'id' );
    }

}