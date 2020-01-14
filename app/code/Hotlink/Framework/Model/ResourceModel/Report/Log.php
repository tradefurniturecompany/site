<?php
namespace Hotlink\Framework\Model\ResourceModel\Report;

class Log extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init( 'hotlink_framework_report_log', 'record_id' );
    }

}
