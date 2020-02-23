<?php
namespace Hotlink\Brightpearl\Model\Monitor\Creditmemo\Queue;

class Config extends \Hotlink\Framework\Model\Monitor\Cron\Config
{

    function getSortOrder( $storeId = null )
    {
        return $this->getConfigData( 'monitor_creditmemo_queue_sort_order', $storeId, 'ASC' );
    }

    function getBatchSize( $storeId = null )
    {
        return $this->getConfigData( 'monitor_creditmemo_queue_batch', $storeId, 50 );
    }

}