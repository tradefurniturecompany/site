<?php
namespace Hotlink\Framework\Model\Report;

class Item implements \Serializable
{

    const FIELD_ID              = 'id';
    const FIELD_MB              = 'mb';
    const FIELD_TIMESTAMP       = 'ts';
    const FIELD_REPORT_SECTION  = 'se';
    const FIELD_REPORT_BATCH    = 'bt';
    const FIELD_MESSAGE_LEVEL   = 'ml';
    const FIELD_MESSAGE         = 'm';
    const FIELD_MESSAGE_DATA    = 'd';
    const FIELD_COUNT_SUCCESS   = 'ss';
    const FIELD_COUNT_FAIL      = 'ff';
    const FIELD_INDENT          = 'i';

    const LEVEL_FATAL   = 1;    // Black
    const LEVEL_ERROR   = 2;    // Red
    const LEVEL_WARN    = 3;    // Orange
    const LEVEL_INFO    = 4;    // Blue
    const LEVEL_DEBUG   = 5;    // Green
    const LEVEL_TRACE   = 6;    //

    protected $_report;
    protected $_id;
    protected $_memory;
    protected $_timestamp;
    protected $_report_section;
    protected $_report_batch;
    protected $_message_level;
    protected $_message;
    protected $_message_data;
    protected $_count_success;
    protected $_count_fail;
    protected $_indent;

    function init( $report, $id, $section, $batch, $level, $message, $success, $fail, $indent )
    {
        $message = str_replace( "\n", '</n>', $message );
        $message = str_replace( "\t", '</t>', $message );
        $message = str_replace( "\r", '</r>', $message );
        $memory = round( memory_get_usage() / 1024 / 1024, 2 );
        $timestamp = microtime( true );
        $this->_report = $report;
        $this->_id = $id;
        $this->_memory = $memory;
        $this->_timestamp = $timestamp;
        $this->_report_section = $section;
        $this->_report_batch = $batch;
        $this->_message_level = $level;
        $this->_message = $message;
        $this->_count_success = $success;
        $this->_count_fail = $fail;
        $this->_indent = $indent;
        return $this;
    }

    function serialize()
    {
        $data = array( self::FIELD_ID             => $this->_id,
                       self::FIELD_MB             => $this->_memory,
                       self::FIELD_TIMESTAMP      => $this->_timestamp,
                       self::FIELD_REPORT_SECTION => $this->_report_section,
                       self::FIELD_REPORT_BATCH   => $this->_report_batch,
                       self::FIELD_MESSAGE_LEVEL  => $this->_message_level,
                       self::FIELD_MESSAGE        => $this->_message,
                       self::FIELD_COUNT_SUCCESS  => $this->_count_success,
                       self::FIELD_COUNT_FAIL     => $this->_count_fail,
                       self::FIELD_INDENT         => $this->_indent
                       );
        return serialize( $data );
    }

    function unserialize( $serialized )
    {
        $data = unserialize( $serialized );
        $this->_id             = $data[ self::FIELD_ID ];
        $this->_memory         = $data[ self::FIELD_MB ];
        $this->_timestamp      = $data[ self::FIELD_TIMESTAMP ];
        $this->_report_section = $data[ self::FIELD_REPORT_SECTION ];
        $this->_report_batch   = $data[ self::FIELD_REPORT_BATCH ];
        $this->_message_level  = $data[ self::FIELD_MESSAGE_LEVEL ];
        $this->_message        = $data[ self::FIELD_MESSAGE ];
        $this->_count_success  = $data[ self::FIELD_COUNT_SUCCESS ];
        $this->_count_fail     = $data[ self::FIELD_COUNT_FAIL ];
        $this->_indent         = $data[ self::FIELD_INDENT ];
        $this->_message        = str_replace( '</n>', "\n", $this->_message );
        $this->_message        = str_replace( '</t>', "\t", $this->_message );
        $this->_message        = str_replace( '</r>', "\r", $this->_message );
    }

    function getReport()
    {
        return $this->_report;
    }

    function getId()
    {
        return $this->_id;
    }

    function getMemory()
    {
        return $this->_memory;
    }

    function getTimestamp()
    {
        return $this->_timestamp;
    }

    function getSection()
    {
        return $this->_report_section;
    }

    function getBatch()
    {
        return $this->_report_batch;
    }

    function getLevel()
    {
        return $this->_message_level;
    }

    function getMessage()
    {
        return $this->_message;
    }

    function getData()
    {
        return $this->_message_data;
    }

    function setData( \Hotlink\Framework\Model\Report\Data $value )
    {
        $this->_message_data = $value;
        return $this;
    }

    function getSuccess()
    {
        return $this->_count_success;
    }

    function getFail()
    {
        return $this->_count_fail;
    }

    function getIndent()
    {
        return $this->_indent;
    }
}
