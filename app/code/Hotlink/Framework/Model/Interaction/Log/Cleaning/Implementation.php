<?php
namespace Hotlink\Framework\Model\Interaction\Log\Cleaning;

class Implementation extends \Hotlink\Framework\Model\Interaction\Implementation\AbstractImplementation
{

    const MAX_BATCH_SIZE = 50;

    protected $logCollectionFactory;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Hotlink\Framework\Model\ResourceModel\Report\Log\CollectionFactory $logCollectionFactory
    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );
        $this->logCollectionFactory = $logCollectionFactory;
    }

    protected function _getName()
    {
        return 'Log cleaning';
    }

    public function execute()
    {
        $report = $this->getReport();
        $environment = $this->getEnvironment();
        $report( $environment, "status" );

        $count = $environment->getParameter( 'count' )->getValue();
        $sleep = $environment->getParameter( 'sleep' )->getValue();

        if ( ( int ) $count === 0 ) {
            $report->error( "Invalid count value" );
            return;
        }

        $countSelect = $this->logCollectionFactory->create()->getSelectCountSql();
        $total = $countSelect->getAdapter()->fetchOne( $countSelect );
        $report->info( "total record count before = $total" );

        if ( $total > 0 ) {
            if ( $count < 0 ) {
                $this->deleteRecords( -$count, $sleep );
            }
            else {
                $count = ( $total > $count ) ? $total - $count : 0;
                $this->deleteRecords( $count, $sleep );
            }
        }

        $countSelect = $this->logCollectionFactory->create()->getSelectCountSql();
        $total = $countSelect->getAdapter()->fetchOne( $countSelect );
        $report->info( "total record count after = $total" );
    }

    protected function deleteRecords( $count, $sleep )
    {
        $report     = $this->getReport();
        $ownLogId   = $report->getId();
        $b = 1;

        while ( $count > 0 ) {

            $batch = min($count, self::MAX_BATCH_SIZE);

            $report->info("Loading log records: batch=$b, size=$batch")->setBatch( $b );

            $collection = $this->logCollectionFactory->create();
            $collection->addOrder( 'record_id', 'ASC' );
            $collection->getSelect()->limit( $batch );

            $ids = [];
            foreach ( $collection as $log ) {
                $logId = $log->getRecordId();
                // do not delete the log this execution creates
                if ( $logId !== $ownLogId ) {

                    try {
                        $this->report()->delete( $logId );
                        $ids[] = $logId;
                        $report->incSuccess();

                        if ( $sleep > 0 ) {
                            usleep( $sleep );
                        }
                    }
                    catch ( \Exception $e ) {
                        $report
                            ->incFail()
                            ->error( "Failed to delete log id $logId", $e );
                    }
                }
            }

            if ( count( $ids ) > 0 ) {
                $ids = implode( ',', $ids );
                $report->debug( "Deleted $ids" );
            }

            $count = max(0, $count - $batch);
            $b++;
        }
    }

}
