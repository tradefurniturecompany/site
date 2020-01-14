<?php
namespace Hotlink\Framework\Model\Stream\Magento\Model\Reader;

class Identifier extends \Hotlink\Framework\Model\Stream\Reader implements \IteratorAggregate
{

    protected $_entities = array();
    protected $_page = 0;

    protected $iteratorFactory;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Hotlink\Framework\Model\Stream\Magento\Model\Reader\Identifier\IteratorFactory $iteratorFactory
    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );
        $this->iteratorFactory = $iteratorFactory;
    }

    public function reset()
    {
        $this->_page = 0;
        return $this;
    }

    protected function _open( \Hotlink\Framework\Model\Filter\Magento $filter, $batchSize = 50 )
    {
        $this->setFilter( $filter );
        $this->_loadBatchSize = $batchSize;
        $ids = $this->getFilter()->getIdentifiers();
        $field = $this->getFilter()->getField();
        $idCount = count( $ids );
        $entities = $this->factory()->create( $this->getFilter()->getModel() )->getCollection();

        if ( method_exists( $entities, 'addAttributeToSelect' ) )
            {
                foreach ( $this->getFilter()->getAttributes() as $code )
                    {
                        $entities->addAttributeToSelect( $code );
                    }
            }
        else if ( method_exists( $entities, 'addFieldToSelect' ) )
            {
                foreach ( $this->getFilter()->getAttributes() as $code )
                    {
                        $entities->addFieldToSelect( $code );
                    }
            }

        if ( ! ( ( $idCount == 1 ) && ( $ids[ 0 ] == '*' ) ) )
            {
                // to support id ranges "{id1}...{id2}", "...{id}" and "{id}..."
                $ranges = array();
                $newids = array();
                foreach ( $ids as $id )
                    {
                        if ( false !== strpos( $id, '...' ) ) // it's a range
                            {
                                $range = array();

                                list( $min, $max, $drop ) = explode( '...', $id, 3 ); // parts including and following second "..." are discarded

                                $min=trim($min);
                                $max=trim($max);
                                if ( $min === $max )
                                    {
                                        $newids[] = $min;
                                    }
                                else
                                    {
                                        if ( '' !== $min )
                                            {
                                                $range['from'] = $min;
                                            }
                                        if ( '' !== $max )
                                            {
                                                $range['to'] = $max;
                                            }
                                        $ranges[] = $range;
                                    }
                            }
                        else
                            {
                                $newids[] = $id;
                            }
                    }

                $hasAddAttributeToFilter = method_exists( $entities, 'addAttributeToFilter' );
                $hasGetSelect = method_exists( $entities, 'getSelect' );

                if ( $hasAddAttributeToFilter )
                    {
                        if ( count( $newids ) )
                            {
                                $ranges[] = array( 'in' => $newids );
                            }
                        $entities->addAttributeToFilter( $field, $ranges );
                    }
                else if ( $hasGetSelect )
                    {
                        $select = $entities->getSelect();
                        $select->where( $field . ' in (?)', $newids );
                        foreach ( $ranges as $range )
                            {
                                $hasFrom = array_key_exists( 'from', $range );
                                $hasTo = array_key_exists( 'to', $range );
                                if ( $hasFrom && $hasTo )
                                    {
                                        $cond = $select->getAdapter()->quoteInto( $field.'>=(?)', $range['from'] );
                                        $select->orWhere( $cond.' AND '.$field.'<=(?)', $range['to'] );
                                    }
                                elseif ( $hasFrom )
                                    {
                                        $select->orWhere( $field.'>=(?)', $range['from'] );

                                    }
                                elseif ( $hasTo )
                                    {
                                        $select->orWhere( $field.'<=(?)', $range['to'] );
                                    }
                            }
                    }
            }

        $entities->setPageSize( $batchSize );
        $this->_entities = $entities;
        return $this;
    }

    protected function _read()
    {
        $this->_page++;
        $loaded = 0;

        //
        //  Use collection slices
        //
        $this->_entities->clear();
        $this->_entities->setCurPage( $this->_page );
        if ( $this->_entities->getCurPage() != $this->_page )
            {
                return false;
            }
        $this->_entities->load();
        return $this->_entities;
    }

    protected function _close()
    {
        $this->_entities = false;
    }

    //  -----------------------------------------------------
    //
    //    IteratorAggreagate
    //
    //  -----------------------------------------------------
    public function getIterator()
    {
        return $this->iteratorFactory->create( [ 'reader' => $this ] );
    }

    //
    //  Interface for Iterator
    //
    public function getCollection()
    {
        return $this->_entities;
    }

}
