<?php
namespace Hotlink\Framework\Model\Interaction\Action;

abstract class AbstractAction extends \Hotlink\Framework\Model\AbstractModel
{

    const REGISTRY_KEY = 'hotlink_framework_runtime_actions';

    protected $_report;

    protected $registry;
    protected $interaction;

    //
    //  Called before an interaction is invoked
    //
    abstract function before();

    //
    //  Called after an interaction has completed and the result is known
    //
    abstract function after();

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Magento\Framework\Registry $registry,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction
    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );

        $this->interaction = $interaction;
        $this->registry = $registry;
        if ( ! ( $actions = $this->registry->registry( self::REGISTRY_KEY) ) )
            {
                $actions = [];
            }
        $name = $this->reflect()->getClass( $this );
        if ( !in_array( $name, $actions ) )
            {
                $actions[] = $name;
            }
        $this->registry->register( self::REGISTRY_KEY, $actions, true );
    }

    function getInteraction()
    {
        return $this->interaction;
    }

    //
    //  IReport
    //
    function setReport( \Hotlink\Framework\Model\Report $report = null )
    {
        $this->_report = $report;
    }

    function getReport( $safe = true )
    {
        if ( !$this->_report && $safe )
            {
                $this->setReport( $this->report()->create( $this ) );
                $writer = $this->report()->logWriterFactory()->create()->open( $this );
                $this->getReport()->addWriter( $writer );
            }
        return $this->_report;
    }

    function getReportSection()
    {
        return 'action';
    }

}
