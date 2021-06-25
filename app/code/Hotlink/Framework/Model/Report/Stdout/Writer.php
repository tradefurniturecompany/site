<?php
namespace Hotlink\Framework\Model\Report\Stdout;

class Writer extends \Hotlink\Framework\Model\Report\Writer\AbstractWriter
{

    const CODE = 'stdout';

    protected $_handle = false;
    protected $_reportId = false;
    protected $_initialTimestamp = null;
    protected $_styled = false;
    protected $_level = 99;

    protected $_contentWidth;

    protected $_width = 80;
    protected $_height = 30;

    protected $styles = array( 'red'         => "[0;31m",   'light_red'     => "[1;31m",
                               'green'       => "[0;32m",   'light_green'   => "[1;32m",
                               'blue'        => "[0;34m",   'light_blue'    => "[1;34m",
                               'grey'        => "[1;30m",   'light_grey'    => "[0;37m",
                               'cyan'        => "[0;36m",   'light_cyan'    => "[1;36m",
                               'magenta'     => "[0;35m",   'light_magenta' => "[1;35m",

                               'yellow'      => "[1;33m",
                               'brown'       => "[0;33m",

                               'white'       => "[1;37m",
                               'black'       => "[0;30m",

                               'normal'      => "[0m",      'bold'        => "[1m",
                               'underscore'  => "[4m",      'reverse'     => "[7m" );

    public function getCode()
    {
        return self::CODE;
    }

    public function setColours( $value )
    {
        $this->_styled = ( bool ) $value;
        return $this;
    }

    public function setLevel( $value )
    {
        $this->_level = $value;
        return $this;
    }

    public function getContentWidth()
    {
        return $this->_contentWidth;
    }

    protected function style( $text, $style="normal" )
    {
        $out = isset( $this->styles[ "$style" ] ) ? $this->styles[ "$style" ] : $this->styles[ "normal" ];
        $ech = chr(27) . "$out" . "$text" . chr(27) . "[0m";
        return $ech;
    }

    protected function _open( \Hotlink\Framework\Model\Report $report )
    {
        parent::_open( $report );

        if ( $reportId = $this->_getReportObject()->getId() )  // ReportId may not be set, in which case we cannot write data
            {
                $this->_reportId = $reportId;
                $this->_handle = fopen('php://stdout', 'w');
                $this->_initDimensions();
            }
    }

    protected function _initDimensions()
    {
        $width = 0;
        try
            {
                $width = exec( 'tput cols' );
            }
        catch ( \Exception $e )
            {
            }
        $this->_width = $width ? $width : 120;

        $height = 0;
        try
            {
                $height = exec( 'tput lines' );
            }
        catch ( \Exception $e )
            {
            }
        $this->_height = $height ? $height : 24;

        $this->_contentWidth = $this->_width - 74;
    }

    protected function _write( \Hotlink\Framework\Model\Report\Item $item )
    {
        if ( $this->_reportId && $this->_handle )
            {
                if ( $item->getLevel() <= $this->_level )
                    {
                        $id        = sprintf( "%-6u"    , $item->getId() );
                        $memory    = sprintf( "%6.2fm"  , $item->getMemory() );
                        $timestamp = sprintf( "%-4us"   , $item->getTimestamp() );
                        $section   = sprintf( "%-15s"   , $item->getSection() );
                        $batch     = sprintf( "%-6u"    , $item->getBatch() );
                        $level     = sprintf( "%s"      , $this->getLevelName( $item->getLevel() ) );

                        $message   = $this->padded( ' ', $item->getIndent(), '' )
                                   . $this->padded( $item->getMessage(), $this->getContentWidth() - $item->getIndent() );

                        $success   = sprintf( "%4us"    , $item->getSuccess() );
                        $fail      = sprintf( "%4uf"    , $item->getFail() );
                        $timestamp = sprintf( "%4.3fs"  , $this->getElapsedTime( $item ) );
                        $indent    = $item->getIndent();
                        if ( $this->_styled )
                            {
                                $id        = $this->style( $id );
                                $memory    = $this->style( $memory );
                                $timestamp = $this->style( $timestamp );
                                $section   = $this->style( $section );
                                $batch     = $this->style( $batch );
                                $level     = $this->style( $level, $this->getLevelStyle( $item->getLevel() ) );
                                $message   = $this->style( $message );
                                $success   = $this->style( $success );
                                $fail      = $this->style( $fail );
                                $timestamp = $this->style( $timestamp );
                                $indent    = $this->style( $indent );
                            }
                        $output = "$id $batch $section [$level] $message $memory / [ $success / $fail ] $timestamp";
                        fwrite( $this->_handle, $output . "\n" );
                    }
            }
        return $this;
    }

    protected function padded( $string, $len, $suffix = "...", $pad_string = " " )
    {
        $stringlen = strlen( $string );
        if ( $stringlen <= $len )
            {
                return str_pad( $string, $len, $pad_string );
            }
        $chopped = substr( $string, 0, $len - strlen( $suffix ) );
        $result = $chopped . $suffix;
        return $result;
    }

    public function getElapsedTime( $item )
    {
        if ( is_null( $this->_initialTimestamp ) )
            {
                $this->_initialTimestamp = $item->getTimestamp();
            }
        $elapsed = round( $item->getTimestamp() - $this->_initialTimestamp, 4 );
        return $elapsed;
    }

    protected function getLevelStyle( $level )
    {
        switch ( $level )
            {
                case \Hotlink\Framework\Model\Report\Item::LEVEL_FATAL:
                    return 'yellow';
                    break;
                case \Hotlink\Framework\Model\Report\Item::LEVEL_ERROR:
                    return 'light_red';
                    break;
                case \Hotlink\Framework\Model\Report\Item::LEVEL_WARN:
                    return 'light_magenta';
                    break;
                case \Hotlink\Framework\Model\Report\Item::LEVEL_INFO:
                    return 'light_blue';
                    break;
                case \Hotlink\Framework\Model\Report\Item::LEVEL_DEBUG:
                    return 'light_green';
                    break;
                case \Hotlink\Framework\Model\Report\Item::LEVEL_TRACE:
                    return 'grey';
                    break;
            }
    }

    protected function getLevelName( $level )
    {
        switch ( $level )
            {
                case \Hotlink\Framework\Model\Report\Item::LEVEL_FATAL:
                    return 'FTL';
                    break;
                case \Hotlink\Framework\Model\Report\Item::LEVEL_ERROR:
                    return 'ERR';
                    break;
                case \Hotlink\Framework\Model\Report\Item::LEVEL_WARN:
                    return 'WRN';
                    break;
                case \Hotlink\Framework\Model\Report\Item::LEVEL_INFO:
                    return 'INF';
                    break;
                case \Hotlink\Framework\Model\Report\Item::LEVEL_DEBUG:
                    return 'DBG';
                    break;
                case \Hotlink\Framework\Model\Report\Item::LEVEL_TRACE:
                    return 'TRC';
                    break;
            }
    }

    protected function _close()
    {
        if ( $this->_handle )
            {
                fclose( $this->_handle );
                $this->_handle = false;
            }
        return $this;
    }

    
}
