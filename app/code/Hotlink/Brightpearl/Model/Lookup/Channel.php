<?php
namespace Hotlink\Brightpearl\Model\Lookup;

class Channel extends \Magento\Framework\Model\AbstractModel
{
    function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\ResourceModel\Lookup\Channel', 'id' );
    }
}