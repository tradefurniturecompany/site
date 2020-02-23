<?php
namespace Hotlink\Framework\Model;

abstract class AbstractModel implements \Hotlink\Framework\Model\Report\IReport
{

    abstract function getReportSection();    // From IReport

    protected $_report = false;

    protected $reflectionHelper;
    protected $exceptionHelper;
    protected $reportHelper;
    protected $factoryHelper;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper
    )
    {
        $this->exceptionHelper = $exceptionHelper;
        $this->reflectionHelper = $reflectionHelper;
        $this->reportHelper = $reportHelper;
        $this->factoryHelper = $factoryHelper;
    }

    function reflect()
    {
        return $this->reflectionHelper;
    }

    function exception()
    {
        return $this->exceptionHelper;
    }

    function report()
    {
        return $this->reportHelper;
    }

    function factory()
    {
        return $this->factoryHelper;
    }

    //
    //   Returns an unique html safe identifier for a class, can be overloaded for more friendly values.
    //
    function getCode()
    {
        return $this->reflect()->getClass( $this );
    }

    function __toString()
    {
        return "[" . $this->getCode() . "]";
    }

    //
    //  IReport
    //
    function setReport( \Hotlink\Framework\Model\Report $report = null )
    {
        $this->_report = $report;
        return $this;
    }

    function getReport( $safe = true )
    {
        if ( !$this->_report && $safe )
            {
                $this->setReport( $this->report()->create( $this ) );
            }
        return $this->_report;
    }

}
