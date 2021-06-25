<?php
namespace Hotlink\Brightpearl\Model\Monitor\Order\Status\Queue;

class Config extends \Hotlink\Framework\Model\Monitor\Cron\Config
{
    function getSortField( $storeId = null )
    {
        return $this->getConfigData( 'monitor_order_status_queue_sort_field', $storeId, 'entity_id' );
    }

    function getSortOrder( $storeId = null )
    {
        return $this->getConfigData( 'monitor_order_status_queue_sort_order', $storeId, 'ASC' );
    }

    function getBatchSize( $storeId = null )
    {
        return $this->getConfigData( 'monitor_order_status_queue_batch', $storeId, 50 );
    }

}