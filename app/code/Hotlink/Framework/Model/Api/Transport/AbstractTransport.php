<?php
namespace Hotlink\Framework\Model\Api\Transport;

abstract class AbstractTransport implements \Hotlink\Framework\Model\Report\IReport, \Serializable, \Hotlink\Framework\Model\Report\IReportData
{

    protected $_report = null;

    protected $_lastRequestHeaders = false;
    protected $_lastRequest = false;
    protected $_lastResponseHeaders = false;
    protected $_lastResponse = false;
    protected $_lastFault = false;

    protected $reportFactory;
    protected $exceptionHelper;

    function __construct(
        \Hotlink\Framework\Model\ReportFactory $reportFactory,
        \Hotlink\Framework\Helper\Exception $exceptionHelper
    )
    {
        $this->reportFactory = $reportFactory;
        $this->exceptionHelper = $exceptionHelper;
    }

    abstract function getProtocol();                                               // A name used in reports
    abstract protected function _submit( \Hotlink\Framework\Model\Api\Request $request );  // Execute the request
    abstract protected function _setLastRequestInfo();                                    // Set transport specific 'last' value here

    protected function _getSerializableFields()
    {
        return array ( '_lastRequestHeaders',
                       '_lastRequest',
                       '_lastResponseHeaders',
                       '_lastResponse');
    }

    protected function _beforeSubmit( \Hotlink\Framework\Model\Api\Request $request )
    {
        $this
            ->_setLastRequestHeaders( false )
            ->_setLastRequest( false )
            ->_setLastResponseHeaders( false )
            ->_setLastResponse( false )
            ->_setLastFault( false );
        return $this;
    }

    function submit( \Hotlink\Framework\Model\Api\Request $request )
    {
        $this->_beforeSubmit( $request );
        $fault = false;
        try
            {
                $result = $this->_submit( $request );
                $this->_setResult( $request, $result );
            }
        catch ( \Hotlink\Framework\Model\Exception\Transport $fault )
            {
                $this->_setLastFault( $fault );
            }
        $this->_reportLastRequestInfo( $request );     // Report info before propagating exception
        if ( $fault )
            {
                throw $fault;
            }
        $this->_afterSubmit( $request );   // Only run after submit if no exception
        return $request->getTransaction()->getResponse();
    }

    protected function _afterSubmit( \Hotlink\Framework\Model\Api\Request $request )
    {
        return $this;
    }

    protected function _setResult( \Hotlink\Framework\Model\Api\Request $request, $result )
    {
        $request->getTransaction()->getResponse()->setContent( $result );  // Api does validation of response, not transport
    }

    protected function _getReportMessagePrefix( \Hotlink\Framework\Model\Api\Request $request )
    {
        return $message = $this->getProtocol() . ' ' . $request->getFunction();
    }

    protected function _reportLastRequestInfo( \Hotlink\Framework\Model\Api\Request $request )
    {
        $this->_setLastRequestInfo();
        if ( $this->_hasLastInfo() )
            {

                $report = $this->getReport( true );     // Need a safe report here
                $report->indent();
                $message = $this->_getReportMessagePrefix( $request );
                $report->debug( $message, $this );
                $report->unindent();
            }
        return $this;
    }

    protected function _hasLastInfo()
    {
        return $this->getLastRequestHeaders()
            || $this->getLastRequest()
            || $this->getLastResponseHeaders()
            || $this->getLastResponse()
            || $this->getLastFault();
    }

    //
    //  Hotlink\Framework\Model\Report\IReport
    //
    function getReport( $safe = true )
    {
        if ( !$this->_report && $safe )
            {
                $this->setReport( $this->reportFactory->create() );
            }
        return $this->_report;
    }

    function setReport( \Hotlink\Framework\Model\Report $report = null )
    {
        $this->_report = $report;
        return $this;
    }

    function getReportSection()
    {
        return 'transport';
    }

    //
    //  Logging info
    //
    function getLastRequestHeaders()
    {
        return $this->_lastRequestHeaders;
    }

    protected function _setLastRequestHeaders( $value )
    {
        $this->_lastRequestHeaders = $value;
        return $this;
    }

    function getLastRequest()
    {
        return $this->_lastRequest;
    }

    protected function _setLastRequest( $value )
    {
        $this->_lastRequest = $value;
        return $this;
    }

    function getLastResponseHeaders()
    {
        return $this->_lastResponseHeaders;
    }

    protected function _setLastResponseHeaders( $value )
    {
        $this->_lastResponseHeaders = $value;
        return $this;
    }

    function getLastResponse()
    {
        return $this->_lastResponse;
    }

    protected function _setLastResponse( $value )
    {
        $this->_lastResponse = $value;
        return $this;
    }

    function getLastFault()
    {
        return $this->_lastFault;
    }

    protected function _setLastFault( $value )
    {
        $this->_lastFault = $value;
        return $this;
    }

    //
    //  Helper functions
    //
    protected function _exceptionHelper()
    {
        return $this->exceptionHelper;
    }

    //
    //  Serializable
    //
    function serialize()
    {
        $data = array();
        $serializableFields = $this->_getSerializableFields();
        foreach( $serializableFields as $property )
            {
                $data[ $property ] = $this->$property;
            }
        return serialize( $data );
    }

    function unserialize( $data )
    {
        $apply = unserialize( $data );
        $serializableFields = $this->_getSerializableFields();
        foreach( $serializableFields as $property )
            {
                if ( array_key_exists( $property, $apply ) )
                    {
                        $this->$property = $apply[ $property ];
                    }
            }
    }

    //
    //  Interface \Hotlink\Framework\Model\Report\IReportData
    //
    function getReportDataRenderer()
    {
        return 'Hotlink\Framework\Block\Adminhtml\Report\Item\Data\Api\Transport';
    }

}
