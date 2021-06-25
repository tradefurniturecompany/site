<?php
namespace Hotlink\Brightpearl\Helper\Api;

abstract class AbstractApi implements \Hotlink\Framework\Model\Report\IReport
{

    abstract public function getName();

    protected $_report;

    protected $exceptionHelper;
    protected $reportHelper;
    protected $brightpearlConfigApi;

    public function __construct(
        \Hotlink\Brightpearl\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Brightpearl\Model\Config\Api $brightpearlConfigApi
    )
    {
        $this->exceptionHelper = $exceptionHelper;
        $this->reportHelper = $reportHelper;
        $this->brightpearlConfigApi = $brightpearlConfigApi;
    }

    public function submit( \Hotlink\Brightpearl\Model\Api\Transaction\AbstractTransaction $transaction,
                            \Hotlink\Brightpearl\Model\Api\Transport\AbstractTransport $transport )
    {
        $report = $this->getReport();
        $request = $transaction->getRequest();
        $request->validate($transaction);

        $report( $transport, 'submit', $request );

        $response = $transaction->getResponse();
        $response->validate();

        return $response;
    }

    protected function _exceptionHelper()
    {
        return $this->exceptionHelper;
    }

    public function exception()
    {
        return $this->exceptionHelper;
    }

    protected function _assertNotEmpty( $name, $value )
    {
        if ( !\Zend_Validate::is( $value, 'NotEmpty' ) )
            {
                $this->exception()->throwApi( "$name cannot be empty" );
            }
        return $this;
    }

    protected function getApiConfig()
    {
        return $this->brightpearlConfigApi;
    }

    //
    //  IReport
    //
    public function getReport($safe = true)
    {
        if ( !$this->_report && $safe )
            {
                $this->setReport( $this->reportHelper->create( $this ) );
            }
        return $this->_report;
    }

    public function setReport( \Hotlink\Framework\Model\Report $report = null )
    {
        $this->_report = $report;
        return $this;
    }

    public function getReportSection()
    {
        return 'api';
    }

}