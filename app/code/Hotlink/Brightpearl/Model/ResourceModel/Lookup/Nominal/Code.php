<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Lookup\Nominal;

class Code extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function _construct()
    {
        $this->_init( 'hotlink_brightpearl_lookup_nominal_code', 'id' );
    }

}