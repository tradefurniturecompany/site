<?php
namespace Hotlink\Brightpearl\Model\Monitor\Queue;

abstract class AbstractQueue extends \Hotlink\Framework\Model\Monitor\AbstractMonitor
{

    const DEFAULT_BATCH_SIZE = 50;

    protected $_processed = [];

    protected function _process( $eventName )
    {
        $report = $this->getReport();
        while ( $entities = $this->getNext() )
            {
                $ids = array_keys( $entities->getItems() );
                $report->info( 'Processing ids: ' . implode( ',', $ids ) );
                $this->trigger( $eventName, [ 'collection' => $entities ] );
                foreach ( $entities as $item )
                    {
                        $this->addProcessed( $item->getId() );
                    }
            }
    }

    protected function addProcessed( $id )
    {
        $this->_processed[ $id ] = $id;
        return $this;
    }

    protected function getProcessed()
    {
        return $this->_processed;
    }

    public function getNext()
    {
        $collection = $this->getList();
        if ( $size = $this->getConfig()->getBatchSize() )
            {
                $collection->setPageSize( $size )->setCurPage( 1 );
            }
        if ( count( $collection ) > 0 )
            {
                return $collection;
            }
        return false;
    }

}