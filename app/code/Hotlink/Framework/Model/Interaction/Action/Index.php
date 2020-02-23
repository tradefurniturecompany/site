<?php
namespace Hotlink\Framework\Model\Interaction\Action;

class Index extends \Hotlink\Framework\Model\Interaction\Action\AbstractAction
{

    protected $_interactions = array();
    protected $_oldIndices = array();

    /**
     * @var \Hotlink\Framework\Helper\Reflection
     */
    protected $interactionReflectionHelper;

    /**
     * @var \Hotlink\Framework\Helper\Clock
     */
    protected $interactionClockHelper;

    protected $indexConfig;
    protected $indexerRegistry;
    protected $configIndexer;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Magento\Framework\Registry $registry,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,

        \Hotlink\Framework\Helper\Reflection $interactionReflectionHelper,
        \Hotlink\Framework\Helper\Clock $interactionClockHelper,
        \Hotlink\Framework\Model\Interaction\Action\Index\ConfigFactory $indexConfigFactory,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\Framework\Indexer\ConfigInterface $configIndexer

    ) {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper, $registry, $interaction );

        $this->interactionReflectionHelper = $interactionReflectionHelper;
        $this->interactionClockHelper = $interactionClockHelper;

        $this->indexConfig = $indexConfigFactory->create( [ 'interaction' => $this->getInteraction() ] );
        $this->indexerRegistry = $indexerRegistry;
        $this->configIndexer = $configIndexer;
    }

    function getReportSection()
    {
        return 'index';
    }

    function before()
    {
        $interaction = $this->getInteraction();
        $name = $this->interactionReflectionHelper->getClass( $interaction );
        $setting = $this->getConfig()->getBefore();
        $this->_interactions[ $name ] = $setting;

        switch ( $setting )
            {
            case \Hotlink\Framework\Model\Config\Field\Indexing\Before\Source::SYSTEM:
                break;
            case \Hotlink\Framework\Model\Config\Field\Indexing\Before\Source::MANUAL:
                $this->getReport()->debug( 'Disabling indexes' );
                $this->setIndices( \Magento\Framework\Mview\View\StateInterface::MODE_ENABLED );
                break;
            case \Hotlink\Framework\Model\Config\Field\Indexing\Before\Source::REALTIME:
                $this->getReport()->debug( 'Enabling realtime indexes' );
                $this->setIndices( \Magento\Framework\Mview\View\StateInterface::MODE_DISABLED );
                break;
            }

    }

    function after()
    {
        if ( $this->getConfig()->getAfter() )
            {
                $startall = $this->clock()->microtime_float();
                $processed = false;
                $indexMsg = '';
                $report = $this->getReport();
                $report->debug( 'Reindexing' )->indent();
                $rebuild = $this->getConfig()->getAfterRebuild();
                if ( count( $rebuild ) > 0 )
                    {
                        $start = $this->clock()->microtime_float();
                        foreach ( $rebuild as $code )
                            {
                                $indexer = $this->indexerRegistry->get($code);
                                if ( $indexer )
                                    {
                                        $report->debug( 'Rebuilding ' . $code );
                                        $indexStart = $this->clock()->microtime_float();

                                        // temporarily enable view
                                        $view = $indexer->getView();
                                        $state = $view->getState();
                                        $oldMode = $state->getMode();
                                        $state->setMode( \Magento\Framework\Mview\View\StateInterface::MODE_ENABLED );
                                        $view->update();

                                        // restore
                                        $state->setMode( $oldMode );

                                        $indexFinish = $this->clock()->microtime_float();
                                        $report->indent()->debug( 'Index rebuilt in ' . ( $indexFinish - $indexStart ) . ' seconds' )->unindent();
                                    }
                            }
                        $finish = $this->clock()->microtime_float();
                        $report->unindent()->debug( 'Completed in ' . ( $finish - $start ) . ' seconds' );
                    }
                else
                    {
                        $report->unindent()->warn( 'No indexes selected for rebuild' );
                    }
            }
        $this->getReport()->debug( 'Restoring indexes modes' );
        $this->restoreIndices();
    }

    protected function setIndices( $mode )
    {
        $this->_oldIndices = [];
        $indersCodes = array_keys( $this->configIndexer->getIndexers() );

        foreach ( $indersCodes as $code ) {
            $indexer = $this->indexerRegistry->get($code);
            $state = $indexer->getView()->getState();

            $this->_oldIndices[ $code ] = $state->getMode();
            $state->setMode( $mode );
        }
    }

    protected function restoreIndices()
    {
        $indersCodes = array_keys( $this->configIndexer->getIndexers() );

        foreach ( $indersCodes as $code ) {
            {
                if ( array_key_exists( $code, $this->_oldIndices ) )
                    {
                        $mode = $this->_oldIndices[ $code ];

                        $indexer = $this->indexerRegistry->get($code);
                        $state = $indexer->getView()->getState();
                        $state->setMode( $mode );
                    }
            }
        }
    }

    protected function clock()
    {
        return $this->interactionClockHelper;
    }

    protected function getConfig()
    {
        return $this->indexConfig;
    }
}
