<?php
namespace Hotlink\Framework\Model\Stream\Magento\Model\Reader;

class Collection extends \Hotlink\Framework\Model\Stream\Reader implements \IteratorAggregate
{

    protected function _open( \Magento\Framework\Data\Collection $filter )
    {
        $this->setFilter( $filter );
        return $this;
    }

    protected function _read()
    {
        return $this;
    }

    protected function _close()
    {
        return $this;
    }

    //  -----------------------------------------------------
    //
    //    IteratorAggregate
    //
    //  -----------------------------------------------------
    public function getIterator()
    {
        return $this->getFilter()->getIterator();
    }

    public function getCollection()
    {
        return $this->_filter;
    }

}
