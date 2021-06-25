<?php
namespace Hotlink\Framework\Model\Stream\Magento\Model;

class Reader extends \Hotlink\Framework\Model\Stream\Reader implements \IteratorAggregate
{
    protected $_reader = false;
    protected $_filter = false;
    protected $_filterInitialised = false;

    //  -----------------------------------------------------
    //
    //    \Hotlink\Framework\Model\Stream\Reader
    //
    //    This implementation is preferred over a factory pattern only for asthetic reasons - you may prefer to invoke the
    //    specialist classes directly and bypass this class.
    //
    //  -----------------------------------------------------


    protected $identifierFactory;
    protected $readerCollectionFactory;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Hotlink\Framework\Model\Stream\Magento\Model\Reader\IdentifierFactory $identifierFactory,
        \Hotlink\Framework\Model\Stream\Magento\Model\Reader\CollectionFactory $readerCollectionFactory
    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );
        $this->identifierFactory = $identifierFactory;
        $this->readerCollectionFactory = $readerCollectionFactory;
    }

    protected function _open()
    {
        $args = func_get_args();
        $filter = $this->getFilter();
        if ( !$filter )
            {
                $filter = $args[ 0 ];
            }
        if ( $filter instanceof \Hotlink\Framework\Model\Filter\Magento )
            {
                $this->_reader = $this->identifierFactory->create();
            }
        else if ( $filter instanceof \Magento\Framework\Data\Collection\AbstractDb )
            {
                $this->_reader = $this->readerCollectionFactory->create();
            }
        else if ( $filter instanceof \Magento\Framework\Data\Collection )
            {
                $this->_reader = $this->readerCollectionFactory->create();
            }
        else
            {
                $this->exception()->throwProcessing( 'Unsupported filter type [' . get_class( $filter ) . '] in [class]', $this );
            }
        call_user_func_array( array( $this->_reader, 'open' ), $args );
        return $this;
    }

    function setFilter( $filter )
    {
        if ( $this->_filterInitialised )
            {
                $this->exception()->throwProcessing( 'Filter already set on [class]', $this );
            }
        $this->_filter = $filter;
        return $this;
    }

    function getFilter()
    {
        return $this->_filter;
    }

    protected function _close()
    {
        call_user_func_array( array( $this->_reader, 'close' ), array() );
        $this->_reader = false;
    }

    protected function _read()
    {
        return call_user_func_array( array( $this->_reader, 'read' ), array() );
    }

    function getReader()
    {
        return $this->_reader;
    }

    //  -----------------------------------------------------
    //
    //    IteratorAggregate
    //
    //  -----------------------------------------------------
    function getIterator()
    {
        return $this->_reader->getIterator();
    }

}
