<?php
namespace Hotlink\Framework\Model\Stream;

abstract class Writer extends \Hotlink\Framework\Model\Stream\Base
{

    public function write()
    {
        $this->confirmOpened();
        $args = func_get_args();
        return call_user_func_array( array( $this, '_write' ), $args );
    }

    public function getWriterId()
    {
        return null;
    }

    //
    // This signature is deliberately undeclared, so that strict typing can be enforced within derived classes
    //
    // abstract protected function _write( $data );

}
