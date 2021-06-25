<?php
namespace Hotlink\Brightpearl\Model\Monitor\Creditmemo\Queue;

class Config extends \Hotlink\Framework\Model\Monitor\Cron\Config
{

    public function getSortOrder( $storeId = null )
    {
        return $this->getConfigData( 'monitor_creditmemo_queue_sort_order', $storeId, 'ASC' );
    }

    public function getBatchSize( $storeId = null )
    {
        return $this->getConfigData( 'monitor_creditmemo_queue_batch', $storeId, 50 );
    }

}