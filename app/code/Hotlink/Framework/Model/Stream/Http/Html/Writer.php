<?php

// @codingStandardsIgnoreFile

namespace Hotlink\Framework\Model\Stream\Http\Html;

class Writer extends \Hotlink\Framework\Model\Stream\Http\Base
{

    const OUTPUT = 1;
    const VARDUMP = 2;
    const PRINTR = 3;

    protected $_htmlTagWritten = false;
    protected $_bufferring = true;
    protected $_renderer = false;

    protected function _open( $sendHeader = true )
    {
        if ( $sendHeader )
            {
                $this->_writeHeader( 'Content-Type: text/html; charset=utf-8' );
            }
        return $this;
    }

    public function emit( $something )
    {
        if ( !$this->_renderer )
            {
                $this->_renderer = new \Hotlink\Framework\Model\Stream\Http\Html\Writer\Renderer();
            }
        $this->_renderer->emit( $something );
    }

    protected function _htmlTag()
    {
        if ( !$this->_htmlTagWritten )
            {
                $this->emit( '<html><body style="font-size: 11px;">' );
                $this->_htmlTagWritten = true;
            }
    }

    protected function _write( $object, $style = self::OUTPUT )
    {
        $this->_htmlTag();
        switch( $style )
            {
            case self::OUTPUT:
                $this->Output( $object );
                break;
            case self::VARDUMP:
                $this->VarDump( $object );
                break;
            case self::PRINTR:
                $this->PrintR( $object );
                break;
            default:
                $this->Output( $object );
                break;
            }
        return $this;
    }

    public function getBufferring()
    {
        return $this->_bufferring;
    }

    public function setBufferring( $value )
    {
        $this->_bufferring = $value;
        return $this;
    }

    protected function Output( $msg )
    {
        if ( $this->getBufferring() )
            {
                $msg = str_pad( $msg, $this->_buffer_size + $this->_buffer_increment );
            }
        $this->emit( $msg );
        flush();
    }

    protected function VarDump( $obj )
    {
        var_dump( $obj );
        if ( $this->getBufferring() )
            {
                $this->emit( str_pad( ' ', $this->_buffer_size + $this->_buffer_increment ) );
            }
        flush();
    }

    protected function PrintR( $obj )
    {
        print_r( $obj  );
        if ( $this->getBufferring() )
            {
                $this->emit( str_pad( ' ', $this->_buffer_size + $this->_buffer_increment ) );
            }
        flush();
    }

    protected function _close()
    {
        $this->_htmlTag();
        $this->Output( '</body></html>' );
        $this->_htmlTagWritten = false;
        return $this;
    }

}
