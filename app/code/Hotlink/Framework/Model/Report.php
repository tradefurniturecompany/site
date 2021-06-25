<?php
namespace Hotlink\Framework\Model;

class Report
{
    /*

      This class is divided into sections

      Instance        handles loading and deleting of reports by id
      Invocation      provides $report() syntax and manages call stack propagation of reports
      Header          properties that are not typically changed during execution (like user)
      Summary         properties that are aggregated during execution (like counters or references)
      State           properties representing specific state at a given time (like status, section or batch)
      Writers         mechanisms for persisting reports to disk
      Reader          mechanisms for reading reports from disk
      Timing          data/time functions
      Messaging       supporting report line item messages

    */

    const STATUS_PROCESSING = 1;
    const STATUS_SUCCESS    = 2;
    const STATUS_ERRORS     = 3;
    const STATUS_EXCEPTION  = 4;
    const STATUS_NO_RESULT  = 5;

    //  -----------------------------------------------------------------------------------------------
    //
    //    Instance
    //
    //  -----------------------------------------------------------------------------------------------
    protected static $_objectCount = 0;
    protected $_objectId = false;
    protected $_reportId = false;

    //  -----------------------------------------------------------------------------------------------
    //
    //    Invocation
    //
    //  -----------------------------------------------------------------------------------------------
    //
    //  A static stack creates garbage collection locks and breaks the module's stack based scoping intentions, so cannot be used.
    //
    //  A protected stack causes segmentation faults due to php design failings (bugs and poor recursion implementation), so cannot be used.
    //
    //     see:  http://bitdepth.thomasrutter.com/2010/05/16/php-recursion-causes-segmentation-fault/
    //           https://bugs.php.net/bug.php?id=1901
    //
    //  Hence the stack is dumbed down to only use strings
    //
    //  Any reference to an object initiates a segmenetation fault (ie. the existence of getOwner() is sufficent to crash everything)
    //
    protected $_nostacktrace = false;

    //  -----------------------------------------------------------------------------------------------
    //
    //    Header
    //
    //  -----------------------------------------------------------------------------------------------
    protected $_user;
    protected $_trigger;
    protected $_context;
    protected $_event;
    protected $_process;

    //  -----------------------------------------------------------------------------------------------
    //
    //    Summary
    //
    //  -----------------------------------------------------------------------------------------------
    const REFERENCE_LEFT = ' ';
    const REFERENCE_RIGHT = '';
    const REFERENCE_SEPARATOR = ',';

    protected $_success = 0;
    protected $_fail = 0;
    protected $_referenceValue = '';
    protected $_referenceCount;
    protected $_detail = null;

    //  -----------------------------------------------------------------------------------------------
    //
    //    State
    //
    //  -----------------------------------------------------------------------------------------------
    protected $_indent = 0;
    protected $_section = false;
    protected $_status = false;
    protected $_batch = false;

    //  -----------------------------------------------------------------------------------------------
    //
    //    Reader
    //
    //  -----------------------------------------------------------------------------------------------
    protected $_reader = false;
    protected $_useDataReader = false;

    //  -----------------------------------------------------------------------------------------------
    //
    //    Writers
    //
    //  -----------------------------------------------------------------------------------------------
    protected $_writers = array();
    protected $_onDestructReportFatalIfOpen = true;
    protected $_rentry = false;
    protected $_items = array();

    //  -----------------------------------------------------------------------------------------------
    //
    //    Timing
    //
    //  -----------------------------------------------------------------------------------------------
    protected $_timeStarted = false;

    //  -----------------------------------------------------------------------------------------------
    //
    //    Messaging
    //
    //  -----------------------------------------------------------------------------------------------
    protected $_itemId = 0;

    //  -----------------------------------------------------------------------------------------------
    //
    //    Instance
    //
    //  -----------------------------------------------------------------------------------------------


    /**
     * @var \Hotlink\Framework\Helper\Report
     */
    protected $interactionReportHelper;

    /**
     * @var \Hotlink\Framework\Model\Report\Log
     */
    protected $interactionReportLog;

    /**
     * @var \Hotlink\Framework\Model\Report\ReaderFactory
     */
    protected $interactionReportReaderFactory;

    /**
     * @var \Hotlink\Framework\Model\Report\Html\WriterFactory
     */
    protected $interactionReportHtmlWriterFactory;

    /**
     * @var \Hotlink\Framework\Model\Report\Log\WriterFactory
     */
    protected $interactionReportLogWriterFactory;

    /**
     * @var \Hotlink\Framework\Model\Report\Item\WriterFactory
     */
    protected $interactionReportItemWriterFactory;

    /**
     * @var \Hotlink\Framework\Model\Report\Data\WriterFactory
     */
    protected $interactionReportDataWriterFactory;

    /**
     * @var \Hotlink\Framework\Model\Report\Stdout\WriterFactory
     */
    protected $interactionReportStdoutWriterFactory;

    /**
     * @var \Hotlink\Framework\Model\Report\DataFactory
     */
    protected $interactionReportDataFactory;

    /**
     * @var \Hotlink\Framework\Model\Report\ItemFactory
     */
    protected $interactionReportItemFactory;

    /**
     * @var \Hotlink\Framework\Helper\Report\Format
     */
    protected $interactionReportFormatHelper;

    /**
     * @var \Hotlink\Framework\Helper\Exception
     */
    protected $interactionExceptionHelper;

    //  -----------------------------------------------------------------------------------------------
    public function __construct(
        $object,
        \Hotlink\Framework\Helper\Report $interactionReportHelper,
        \Hotlink\Framework\Model\Report\Log $interactionReportLog,
        \Hotlink\Framework\Model\Report\ReaderFactory $interactionReportReaderFactory,
        \Hotlink\Framework\Model\Report\Html\WriterFactory $interactionReportHtmlWriterFactory,
        \Hotlink\Framework\Model\Report\Log\WriterFactory $interactionReportLogWriterFactory,
        \Hotlink\Framework\Model\Report\Item\WriterFactory $interactionReportItemWriterFactory,
        \Hotlink\Framework\Model\Report\Data\WriterFactory $interactionReportDataWriterFactory,
        \Hotlink\Framework\Model\Report\Stdout\WriterFactory $interactionReportStdoutWriterFactory,
        \Hotlink\Framework\Model\Report\DataFactory $interactionReportDataFactory,
        \Hotlink\Framework\Model\Report\ItemFactory $interactionReportItemFactory,
        \Hotlink\Framework\Helper\Report\Format $interactionReportFormatHelper,
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper,
        $status = self::STATUS_PROCESSING )
    {
        $this->interactionReportHelper = $interactionReportHelper;
        $this->interactionReportLog = $interactionReportLog;
        $this->interactionReportReaderFactory = $interactionReportReaderFactory;
        $this->interactionReportHtmlWriterFactory = $interactionReportHtmlWriterFactory;
        $this->interactionReportLogWriterFactory = $interactionReportLogWriterFactory;
        $this->interactionReportItemWriterFactory = $interactionReportItemWriterFactory;
        $this->interactionReportDataWriterFactory = $interactionReportDataWriterFactory;
        $this->interactionReportStdoutWriterFactory = $interactionReportStdoutWriterFactory;
        $this->interactionReportDataFactory = $interactionReportDataFactory;
        $this->interactionReportItemFactory = $interactionReportItemFactory;
        $this->interactionReportFormatHelper = $interactionReportFormatHelper;
        $this->interactionExceptionHelper = $interactionExceptionHelper;
        $this->_objectId = ++self::$_objectCount;
        $this->setSection( $object );
        $this->setStatus( $status );
        $this->setTimeStarted( $this->getNow() );
    }

    public function getObjectId()
    {
        return $this->_objectId;
    }

    /* public static function delete( $id ) */
    /* { */
    /*     // Remove item file */
    /*     $itemFile = $this->interactionReportHelper->getFilePath( $id, false ); */
    /*     if ( file_exists( $itemFile ) ) */
    /*         { */
    /*             unlink( $itemFile ); */
    /*         } */

    /*     // Remove item data files */
    /*     $itemDataFiles = glob( $itemFile . ".*" ); */
    /*     foreach ( $itemDataFiles as $itemDataFile ) */
    /*         { */
    /*             if ( is_file( $itemDataFile ) && file_exists( $itemDataFile ) ) */
    /*                 { */
    /*                     unlink( $itemDataFile ); */
    /*                 } */
    /*         } */

    /*     $entry = $this->interactionReportLog->load( $id ); */
    /*     $entry->delete(); */
    /*     return true; */
    /* } */

    public function load( $id )
    {
        if ( $this->getId() )
            {
                $this->_getExceptionHelper()->throwImplementation( "Report already loaded", $this );
            }
        $this->setId( $id );
        $this->getReader()->open( $this );
        return $this;
    }

    public function getId()
    {
        if ( ! $this->_reportId )
            {
                if ( $writer = $this->getWriter( 'log' ) )
                    {
                        $this->_reportId = $writer->getLog()->getId();
                    }
            }
        return $this->_reportId;
    }

    public function setId( $value )
    {
        $this->_reportId = $value;
        return $this;
    }

    //  -----------------------------------------------------------------------------------------------
    //
    //    Invocation
    //
    //  -----------------------------------------------------------------------------------------------
    public function __invoke( $object, $method )
    {
        $args = func_get_args();
        $args = array_slice( $args, 2 );

        //
        //  Smart layout (that only indent when no changes made by caller) would be nice, however log save trace messages yield
        //  resulting report layout unpredictable.
        //
        $indent = $this->getIndent();
        if ( ! $this->_nostacktrace )
            {
                $this->trace( 'executing ' . get_class( $object ) . '->' . $method );
                $this->indent();
            }
        $this->_nostacktrace = false;

        $section = $this->getSection();
        $this->setSection( $object );

        if ( $supported = ( $object instanceof \Hotlink\Framework\Model\Report\IReport ) )
            {
                $previous = $object->getReport( false );
                $object->setReport( $this );
            }
        $result = false;
        $exception = false;

        try
            {
                $result = call_user_func_array( array( $object, $method ), $args );
            }
        catch ( \Exception $exception )
            {
            }
        $this->setIndent( $indent );
        if ( $supported )
            {
                if ( !$previous )
                    {
                        $previous = null;
                    }
                $object->setReport( $previous );
            }

        $this->setSection( $section );

        if ( $exception )
            {
                throw $exception;
            }
        return $result;
    }

    //
    //  Disables the emittion of the call stack trace on the next invocation
    //
    public function nostacktrace()
    {
        $this->_nostacktrace = true;
        return $this;
    }

    //  -----------------------------------------------------------------------------------------------
    //
    //    Header
    //
    //  -----------------------------------------------------------------------------------------------
    public function getUser()
    {
        return $this->_user;
    }

    public function setUser( $value )
    {
        $this->_user = $value;
        return $this;
    }

    public function getTrigger()
    {
        return $this->_trigger;
    }

    public function setTrigger( $value )
    {
        $this->_trigger = $value;
        return $this;
    }

    public function getContext()
    {
        return $this->_context;
    }

    public function setContext( $value )
    {
        $this->_context = $value;
        return $this;
    }

    public function getEvent()
    {
        return $this->_event;
    }

    public function setEvent( $value )
    {
        $this->_event = $value;
        return $this;
    }

    public function getProcess()
    {
        return $this->_process;
    }

    public function setProcess( $value )
    {
        $this->_process = $value;
        return $this;
    }

    //  -----------------------------------------------------------------------------------------------
    //
    //    Summary
    //
    //  -----------------------------------------------------------------------------------------------
    //
    //   Success
    //
    public function incSuccess()
    {
        $this->_success = $this->_success + 1;
        return $this;
    }

    public function getSuccess()
    {
        return $this->_success;
    }

    public function setSuccess( $value )
    {
        $this->_success = $value;
        return $this;
    }

    public function succeeded()
    {
        return ( $this->_success > 0 );
    }

    //
    //   Fail
    //
    public function incFail()
    {
        $this->_fail = $this->_fail + 1;
        return $this;
    }

    public function getFail()
    {
        return $this->_fail;
    }

    public function setFail( $value )
    {
        $this->_fail = $value;
        return $this;
    }

    public function failed()
    {
        return ( $this->_fail > 0 );
    }

    //
    //   Reference
    //
    public function addReference( $ref )
    {
        if ( is_array( $ref ) )
            {
                foreach ( $ref as $item )
                    {
                        $this->addReference( $item );
                    }
            }
        else
            {
                $unique = self::REFERENCE_LEFT . ( string ) $ref . self::REFERENCE_RIGHT;
                if ( strpos( $this->_referenceValue, $unique ) === false )
                    {
                        $this->_referenceValue .= ( strlen( $this->_referenceValue ) > 0 )
                                               ? self::REFERENCE_SEPARATOR . $unique
                                               : $unique;
                        $this->_referenceCount++;
                    }
            }
        return $this;
    }

    public function getReference()
    {
        return $this->_referenceValue;
    }

    public function getReferenceCount()
    {
        return $this->_referenceCount;
    }

    //  -----------------------------------------------------------------------------------------------
    //
    //    State
    //
    //  -----------------------------------------------------------------------------------------------
    //
    //   Indent
    //
    public function indent()
    {
        ++$this->_indent;
        return $this;
    }

    public function unindent()
    {
        $this->_indent = max( $this->_indent - 1, 0 );
        return $this;
    }

    public function getIndent()
    {
        return $this->_indent;
    }

    public function setIndent( $val )
    {
        $val = ( int ) $val;
        $this->_indent = ( $val > 0 ) ? $val : 0;
        return $this;
    }

    //
    //   Section
    //
    public function getSection()
    {
        return $this->_section;
    }

    public function setSection( $something )
    {
        if ( is_string( $something ) )
            {
                $this->_section = $something;
            }
        else
            {
                if ( is_array( $something ) )
                    {
                        $this->_section = '';
                    }
                else
                    {
                        $this->_section = ( $something instanceof \Hotlink\Framework\Model\Report\IReport )
                            ? $something->getReportSection()
                            : get_class( $something );
                    }
            }
        return $this;
    }

    //
    //   Status
    //
    public function getStatus()
    {
        return $this->_status;
    }

    public function setStatus( $code )
    {
        $this->_status = $code;
        return $this;
    }

    //
    //   Batch
    //
    public function setBatch( $value )
    {
        $this->_batch = $value;
        return $this;
    }

    public function getBatch()
    {
        return $this->_batch;
    }

    //  -----------------------------------------------------------------------------------------------
    //
    //    Reader
    //
    //  -----------------------------------------------------------------------------------------------
    public function getReader()
    {
        if ( ! $this->_reader )
            {
                $this->_reader = $this->interactionReportReaderFactory->create();
            }
        return $this->_reader;
    }

    public function read()
    {
        $reader = $this->getReader();
        if ( ! $reader->isOpen() )
            {
                $reader->open( $this );
            }
        $item = $reader->read();
        if ( ! $item )
            {
                $reader->close();
            }
        return $item;
    }

    public function setUseDataReader( $value = true )
    {
        $this->_useDataReader = $value;
        return $this;
    }

    public function getUseDataReader()
    {
        return $this->_useDataReader;
    }

    //  -----------------------------------------------------------------------------------------------
    //
    //    Writers
    //
    //  -----------------------------------------------------------------------------------------------
    public function addHtmlWriter( $sendHeader = true, $sendJs = true, $restrictLevels = array() )
    {
        $writer = $this->interactionReportHtmlWriterFactory->create();
        $writer->open( $this, $sendHeader, $sendJs, $restrictLevels );
        $this->addWriter( $writer );
        return $this;
    }

    public function addLogWriter()
    {
        $writer = $this->interactionReportLogWriterFactory->create();
        $this->addWriter( $writer );
        return $this;
    }

    public function addItemWriter()
    {
        $writer = $this->interactionReportItemWriterFactory->create();
        $this->addWriter( $writer );
        return $this;
    }

    public function addDataWriter()
    {
        $writer = $this->interactionReportDataWriterFactory->create();
        $this->addWriter( $writer );
        return $this;
    }

    public function addStdoutWriter()
    {
        $writer = $this->interactionReportStdoutWriterFactory->create();
        $this->addWriter( $writer );
        return $this;
    }

    public function getWriters()
    {
        return $this->_writers;
    }

    public function getWriter( $code )
    {
        foreach( $this->getWriters() as $writer )
            {
                if ( $writer->getCode() == $code )
                    {
                        return $writer;
                    }
            }
        return false;
    }

    public function addWriter( \Hotlink\Framework\Model\Stream\Writer $writer )
    {
        $this->_writers[] = $writer;
        return $this;
    }

    public function write( \Hotlink\Framework\Model\Report\Item $item )
    {
        $this->_items[] = $item;
        if ( $this->_rentry )
            {
                return;
            }
        $this->_rentry = true;
        while ( count( $this->_items ) > 0 )
            {
                $item = array_shift( $this->_items );
                $this->_write( $item );
            }
        $this->_rentry = false;
        return $this;
    }

    protected function _write( \Hotlink\Framework\Model\Report\Item $item )
    {
        foreach ( $this->getWriters() as $writer )
            {
                if ( ! $writer->isOpen() )
                    {
                        $writer->open( $this );
                    }
                $writer->write( $item );
            }
        return $this;
    }

    public function close()
    {
        foreach ( $this->getWriters() as $writer )
            {
                if ( $writer->isOpen() )
                    {
                        $writer->close();
                    }
            }
    }

    public function setOnDestructReportFatalIfOpen( $value )
    {
        $this->_onDestructReportFatalIfOpen = ( bool ) $value;
        return $this;
    }

    public function __destruct()
    {
        try
            {
                $openWriters = 0;
                foreach ( $this->getWriters() as $writer )
                    {
                        if ( $writer->isOpen() )
                            {
                                $openWriters++;
                            }
                    }
                if ( $this->_onDestructReportFatalIfOpen )
                    {
                        if ( $openWriters > 0 )
                            {
                                $error = error_get_last();
                                if ( $error !== null )
                                    {
                                        $this->fatal( "Fatal shutdown", $error );
                                    }
                                else
                                    {
                                        $this->fatal( "Fatal shutdown" );
                                    }
                            }
                    }
                foreach ( $this->getWriters() as $writer )
                    {
                        if ( $writer->isOpen() )
                            {
                                $writer->close();
                            }
                    }
            }
        catch ( \Exception $e )  // in case exception thrown within destructor, avoids nasty "Stack frame 0" error message
            {
            }
    }

    //  -----------------------------------------------------------------------------------------------
    //
    //    Timing
    //
    //  -----------------------------------------------------------------------------------------------
    public function setTimeStarted( $value )
    {
        $this->_timeStarted = $value;
        return $this;
    }

    public function getTimeStarted()
    {
        return $this->_timeStarted;
    }

    public function getNow()
    {
        return microtime( true );
    }

    //  -----------------------------------------------------------------------------------------------
    //
    //    Messaging
    //
    //  -----------------------------------------------------------------------------------------------
    public function fatal( $message, $data = false, $dataRenderer = false )
    {
        return $this->add( 'fatal', $message, $data, $dataRenderer );
    }

    public function error( $message, $data = false, $dataRenderer = false )
    {
        return $this->add( 'error', $message, $data, $dataRenderer );
    }

    public function warn( $message, $data = false, $dataRenderer = false )
    {
        return $this->add( 'warn', $message, $data, $dataRenderer );
    }

    public function info( $message, $data = false, $dataRenderer = false )
    {
        return $this->add( 'info', $message, $data, $dataRenderer );
    }

    public function debug( $message, $data = false, $dataRenderer = false )
    {
        return $this->add( 'debug', $message, $data, $dataRenderer );
    }

    public function trace( $message, $data = false, $dataRenderer = false )
    {
        return $this->add( 'trace', $message, $data, $dataRenderer );
    }

    //
    //  Create a new item with a unique id, and write it to the report
    //
    public function add( $type, $message, $data, $dataRenderer )
    {
        $itemId = $this->_itemId;
        $itemId++;

        if ( $message instanceof \Exception )
            {
                if ( !$data )
                    {
                        $data = $message;
                    }
                $message = $data->getMessage();
            }
        $object = false;
        if ( $data )
            {
                if ( $data instanceof \Exception )
                    {
                        if ( ! ( $data instanceof \Hotlink\Framework\Model\Report\Data\Exception ) )  // Wrap exceptions for safe serialization
                            {
                                $data = new \Hotlink\Framework\Model\Report\Data\Exception( $data->getMessage(), $data->getCode(), $data );
                            }
                    }
                if ( $data instanceof \Hotlink\Framework\Model\Report\Data )
                    {
                        $object = $data;
                    }
                else
                    {
                        $object = $this->interactionReportDataFactory->create();
                        $object->init( $itemId, $data, $dataRenderer );
                    }
                if ( $dataRenderer )  // Overload renderer as requested
                    {
                        $object->setRenderer( $dataRenderer );
                    }
            }

        $item = $this->interactionReportItemFactory->create();

        $section = $this->getSection();
        $batch = $this->getBatch();
        $indent = $this->getIndent();
        $success = $this->getSuccess();
        $fail = $this->getFail();
        $level = $this->_getLevel( $type );

        $item->init( $this, $itemId, $section, $batch, $level, $message, $success, $fail, $indent );
        if ( $object )
            {
                $item->setData( $object );
            }

        $this->_itemId = $itemId;
        $this->write( $item );
        return $this;
    }

    protected function _getLevel( $type )
    {
        switch ( $type )
            {
            case 'trace':
                return \Hotlink\Framework\Model\Report\Item::LEVEL_TRACE;
                break;
            case 'debug':
                return \Hotlink\Framework\Model\Report\Item::LEVEL_DEBUG;
                break;
            case 'info':
                return \Hotlink\Framework\Model\Report\Item::LEVEL_INFO;
                break;
            case 'warn':
                return \Hotlink\Framework\Model\Report\Item::LEVEL_WARN;
                break;
            case 'error':
                return \Hotlink\Framework\Model\Report\Item::LEVEL_ERROR;
                break;
            case 'fatal':
                return \Hotlink\Framework\Model\Report\Item::LEVEL_FATAL;
                break;
            default:
                break;
            }
        return \Hotlink\Framework\Model\Report\Item::LEVEL_INFO;
    }

    protected function _stringify( $message, $showExceptionTrace = false )
    {
        if ( is_object( $message ) )
            {
                if ( $message instanceof \Exception )
                    {
                        $text = $message->getMessage();
                        if ( $showExceptionTrace )
                            {
                                $text .= "\n\n";
                                $text .= $message->getTraceAsString();
                            }
                        $message = $text;
                    }
                else
                    {
                        $message = ( string ) $message;
                    }
            }
        return $message;
    }

    public function getFormat()
    {
        return $this->interactionReportFormatHelper;
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_PROCESSING => 'processing',
            self::STATUS_SUCCESS    => 'success',
            self::STATUS_ERRORS     => 'errors',
            self::STATUS_EXCEPTION  => 'exception',
            self::STATUS_NO_RESULT  => 'no_result'
        ];
    }

    protected function _getExceptionHelper()
    {
        return $this->interactionExceptionHelper;
    }
}
