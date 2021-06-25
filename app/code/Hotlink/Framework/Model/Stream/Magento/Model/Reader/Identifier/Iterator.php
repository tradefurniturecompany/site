<?php
namespace Hotlink\Framework\Model\Stream\Magento\Model\Reader\Identifier;

class Iterator implements \Iterator
{

    protected $_reader;
    protected $_index = false;
    protected $_count = false;
    protected $_items = false;
    protected $_iterator = false;
    protected $_expired = false;

    function __construct( $reader )
    {
        //$args = func_get_args();
        //$this->_reader = $args[ 0 ];
        $this->_reader = $reader;
    }

    protected function getReader()
    {
        return $this->_reader;
    }

    protected function refresh()
    {
        $this->_items = $this->getReader()->read();
        if ( $this->_items )
            {
                $this->_iterator = $this->_items->getIterator();
                $this->_count = count( $this->_items );
                $this->_index = 1;
                $this->_expired = false;
                reset( $this->_iterator );
            }
        else
            {
                $this->_expired = true;
            }
    }

    //  -----------------------------------------------------
    //
    //    Iterator
    //
    //  -----------------------------------------------------
    function current()
    {
        return $this->_iterator->current();
    }

    function key()
    {
        return $this->_iterator->key();
    }

    function next()
    {
        if ( $this->_index > $this->_count )
            {
                $this->refresh();
            }
        $this->_index++;
        return $this->_iterator->next();
    }

    function rewind()
    {
        $this->getReader()->reset();
        $this->refresh();
    }

    function valid()
    {
        if ( ! $this->_items || ( $this->_index > $this->_count ) )
            {
                $this->refresh();
            }
        if ( !$this->_expired && ( $this->_index <= $this->_count ) )
            {
                return $this->_iterator->valid();
            }
        return false;
    }

}
