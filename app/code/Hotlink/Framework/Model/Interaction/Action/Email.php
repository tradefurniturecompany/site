<?php
namespace Hotlink\Framework\Model\Interaction\Action;

class Email extends \Hotlink\Framework\Model\Interaction\Action\AbstractAction
{

    const SUBJECT_SUCCESS = 'Success Report : [interaction]';
    const SUBJECT_ERROR = 'Error Report : [interaction]';

    protected $emailHelper;
    protected $emailConfig;
    protected $stringWriterFactory;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Magento\Framework\Registry $registry,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,

        \Hotlink\Framework\Helper\Emailer $emailHelper,
        \Hotlink\Framework\Model\Interaction\Action\Email\ConfigFactory $emailConfigFactory,
        \Hotlink\Framework\Model\Stream\Http\Text\WriterFactory $stringWriterFactory
    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper, $registry, $interaction );

        $this->emailHelper = $emailHelper;
        $this->emailConfig = $emailConfigFactory->create( [ 'interaction' => $this->getInteraction() ] );
        $this->stringWriterFactory = $stringWriterFactory;
    }

    public function getReportSection()
    {
        return 'email';
    }

    public function before()
    {
    }

    public function after()
    {
        $to = [];
        $errors = null;
        $interaction = $this->getInteraction();
        if ( $interaction->getReport()->getFail() > 0 )
            {
                $to = $this->_getConfig()->getFail();
                $errors = true;
            }
        else if ( $interaction->getReport()->getSuccess() > 0 )
            {
                $to = $this->_getConfig()->getSuccess();
                $errors = false;
            }
        if ( count ( $to ) > 0 )
            {
                $subject = ( $errors ) ? self::SUBJECT_ERROR : self::SUBJECT_SUCCESS;
                $subject = $this->_prepareSubject( $subject, $interaction );

                $headers = $this->_clearHeaders();
                $content = $this->_getContent( $interaction->getReport()->getId() );
                $this->_restoreHeaders( $headers );

                $this->emailHelper->send( $to, $subject, $content );
                $this->getReport()->debug( 'Emailed ' . $subject . ' to ' . implode( ',', $to )  );
            }
        else
            {
                $this->getReport()->trace( 'Not configured' );
            }
    }

    protected function _clearHeaders()
    {
        $headers = array();
        if ( ! headers_sent() )
            {
                foreach ( headers_list() as $header )
                    {
                        $headers[] = $header;
                        header_remove( $header );
                    }
            }
        return $headers;
    }

    protected function _restoreHeaders( $headers )
    {
        if ( ! headers_sent() )
            {
                foreach ( $headers as $header )
                    {
                        header( $header );
                    }
            }
    }

    protected function _prepareSubject( $subject, $interaction )
    {
        $subject = str_replace( '[interaction]', $interaction->getName(), $subject );
        $statuses = \Hotlink\Framework\Model\Report::getStatusOptions();
        $intStatus = $interaction->getReport()->getStatus();
        if ( array_key_exists( $intStatus, $statuses ) && !empty( $statuses[ $intStatus ] ) )
            {
                $subject .= ' [' . $statuses[ $intStatus ] . ']';
            }
        return $subject;
    }

    protected function _getContent( $reportId )
    {
        $onlyLevels = $this->_getConfig()->getEmailLevelsInclude();

        //
        //  $this->getReport() supports IReport and is used for invocation, not suitable for use here where we need a new object.
        //
        $report = $this->report()->create( $this );
        $report->load( $reportId );
        $report->addHtmlWriter( false, false, $onlyLevels );
        $htmlWriter = $report->getWriter( 'html' );
        $stringWriter = $this->_getStringWriter();
        $stringWriter->open();
        $htmlWriter->setDirectHtmlWriter( $stringWriter );
        $report->setUseDataReader( false );              // Do not send data
        // $report->setExcludeExternalJs( true );        // This method needs to be available on the html writer
        while ( $item = $report->read() )
            {
                $report->write( $item );
            }
        $report->close();
        $content = $stringWriter->getHtml();
        return $content;
    }

    protected function _getStringWriter()
    {
        return $this->stringWriterFactory->create();
    }

    protected function _getConfig()
    {
        return $this->emailConfig;
    }

}