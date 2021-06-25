<?php
namespace Hotlink\Framework\Model\Stream;

abstract class Reader extends \Hotlink\Framework\Model\Stream\Base
{
    //
    // If Traversable could be explcitly delcared, this class would implement it.
    // This class cannot implement Iterator, because child classes may alternatively implement Iterator or IteratorAggregate.
    //

    //
    // This signatures is deliberately undeclared, so that strict typing can be enforced within derived classes
    //
    // abstract protected function _read();
    //

    function read()
    {
        $this->confirmOpened();
        $args = func_get_args();
        return call_user_func_array( array( $this, '_read' ), $args );
    }

    protected $_filter;

    function getFilter()
    {
        return $this->_filter;
    }

    protected function setFilter( $value )
    {
        $this->_filter = $value;
    }

}
