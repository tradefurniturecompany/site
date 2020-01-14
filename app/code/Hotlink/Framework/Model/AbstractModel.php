<?php
namespace Hotlink\Framework\Model;

abstract class AbstractModel implements \Hotlink\Framework\Model\Report\IReport
{

    abstract public function getReportSection();    // From IReport

    protected $_report = false;

    protected $reflectionHelper;
    protected $exceptionHelper;
    protected $reportHelper;
    protected $factoryHelper;

    public function __construct(
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

    public function reflect()
    {
        return $this->reflectionHelper;
    }

    public function exception()
    {
        return $this->exceptionHelper;
    }

    public function report()
    {
        return $this->reportHelper;
    }

    public function factory()
    {
        return $this->factoryHelper;
    }

    //
    //   Returns an unique html safe identifier for a class, can be overloaded for more friendly values.
    //
    public function getCode()
    {
        return $this->reflect()->getClass( $this );
    }

    public function __toString()
    {
        return "[" . $this->getCode() . "]";
    }

    //
    //  IReport
    //
    public function setReport( \Hotlink\Framework\Model\Report $report = null )
    {
        $this->_report = $report;
        return $this;
    }

    public function getReport( $safe = true )
    {
        if ( !$this->_report && $safe )
            {
                $this->setReport( $this->report()->create( $this ) );
            }
        return $this->_report;
    }

}
