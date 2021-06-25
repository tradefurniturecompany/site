<?php
namespace Hotlink\Brightpearl\Model\Api\Transport;

abstract class AbstractTransport extends \Hotlink\Framework\Model\Api\Transport\AbstractTransport
{
    const DEFAULT_TIMEOUT = 5000;
    const DEFAULT_CONNECT_TIMEOUT = 5000;

    protected $_client = null;
    protected $_baseUrl;
    protected $_timeout;
    protected $_connectTimeout;

    /**
     * @var \Hotlink\Brightpearl\Helper\Exception
     */
    protected $brightpearlExceptionHelper;

    function __construct(
        \Hotlink\Framework\Model\ReportFactory $interactionReportFactory,
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper,
        \Hotlink\Brightpearl\Helper\Exception $brightpearlExceptionHelper
    ) {
        $this->brightpearlExceptionHelper = $brightpearlExceptionHelper;

        parent::__construct($interactionReportFactory, $interactionExceptionHelper);
    }

    function getProtocol()
    {
        return 'curl';
    }

    function setBaseUrl($url, $secure = true)
    {
        $url = str_replace('http://', '', $url);
        $url = str_replace('https://', '', $url);

        if($secure)
            $url = "https://$url";
        else
            $url = "http://$url";

        $this->_baseUrl = $url;
        return $this;
    }

    function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    function setTimeout($milliseconds)
    {
        $this->_timeout = $milliseconds;
        return $this;
    }

    function getTimeout()
    {
        return $this->_timeout ? $this->_timeout : self::DEFAULT_TIMEOUT;
    }

    function setConnectionTimeout($milliseconds)
    {
        $this->_connectTimeout = $milliseconds;
        return $this;
    }

    function getConnectionTimeout()
    {
        return $this->_connectTimeout ? $this->_connectTimeout : self::DEFAULT_CONNECT_TIMEOUT;
    }

    protected function validate()
    {
        if (!\Zend_Validate::is($this->getBaseUrl(), 'NotEmpty'))
            $this->_exceptionHelper()->throwTransport('Missing required [baseUrl]');

        try {
            \Zend_Uri::factory($this->getBaseUrl());
        }
        catch( \Zend_Uri_Exception $e) {
            $this->_exceptionHelper()->throwTransport('Invalid baseUrl: '.$e->getMessage());
        }

        return $this;
    }

    function getReport($safe = true)
    {
        if (!$this->_report && $safe)
            $this->setReport($this->interactionReportFactory->create( [ 'object' => $this ] ));
        return $this->_report;
    }

    function setReport(\Hotlink\Framework\Model\Report $report = null)
    {
        $this->_report = $report;
        return $this;
    }

    function getReportSection()
    {
        return 'transport';
    }

    protected function _exceptionHelper()
    {
        return $this->brightpearlExceptionHelper;
    }
}
