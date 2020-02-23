<?php
namespace Hotlink\Framework\Model\Stream\Http\Text;

class Writer extends \Hotlink\Framework\Model\Stream\Http\Base
{

    protected $_htmlTagWritten = false;
    protected $_bufferring = true;
    protected $_html = '';

    protected function _open( $sendHeader = true )
    {
        $this->_html = '';
        return $this;
    }

    protected function _htmlTag()
    {
        if ( !$this->_htmlTagWritten )
            {
                $this->output( '<html><body style="font-size: 11px;">' );
                $this->_htmlTagWritten = true;
            }
    }

    protected function _write( $message )
    {
        $this->_htmlTag();
        $this->output( $message );
        return $this;
    }

    function output( $string )
    {
        $this->_html .= $string;
    }

    protected function _close()
    {
        $this->_htmlTag();
        $this->output( '</body></html>' );
        $this->_htmlTagWritten = false;
        return $this;
    }

    function getHtml()
    {
        return $this->_html;
    }

}
