<?php
namespace Hotlink\Framework\Model\Stream;

abstract class Base extends \Hotlink\Framework\Model\AbstractModel
{

    //
    // These signatures are deliberately undeclared, so that strict typing can be enforced within derived classes
    //
    // abstract protected function _open( $data );
    // abstract protected function _close( $data );
    //

    protected $_opened = false;
    protected $_report = null;

    protected function confirmOpened()
    {
        if ( !$this->isOpen() )
            {
                $this->exception()->throwImplementation( 'Stream [class] has not been opened', $this );
            }
    }

    function isOpen()
    {
        return $this->_opened;
    }

    protected function confirmClosed()
    {
        if ( $this->isOpen() )
            {
                $this->exception()->throwImplementation( 'Stream [class] has already been opened', $this );
            }
    }

    function open()
    {
        $this->confirmClosed();
        $args = func_get_args();
        $result = call_user_func_array( array( $this, '_open' ), $args );
        $this->_opened = true;
        return $result;
    }

    function close()
    {
        if ( $this->isOpen() )
            {
                $args = func_get_args();
                $result = call_user_func_array( array( $this, '_close' ), $args );
                $this->_opened = false;
                return $result;
            }
        return false;
    }

    //
    //  IReport
    //
    function getReportSection()
    {
        return 'stream';
    }

}
