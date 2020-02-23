<?php
namespace Hotlink\Framework\Model\ResourceModel\Report\Log;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'report_id';

    function _construct()
    {
        $this->_init( 'Hotlink\Framework\Model\Report\Log',  'Hotlink\Framework\Model\ResourceModel\Report\Log' );
    }
}
