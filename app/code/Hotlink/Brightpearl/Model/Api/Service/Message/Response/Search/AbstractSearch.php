<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Message\Response\Search;

abstract class AbstractSearch extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    function getResults()
    {
        if ( $results = $this->_get( array('response', 'results') ) ) {
            $_results = array();
            $columns  = $this->_getColumnNames();

            foreach ( $results as $_result ) {
                $_results[] = array_combine( $columns, $_result );
            }

            return $_results;
        }
    }

    function getMetaData()
    {
        return $this->_get( array('response', 'metaData') );
    }

    function getPagination()
    {
        $meta    = $this->getMetaData();
        $pagKeys = array( 'morePagesAvailable',
                          'resultsAvailable',
                          'resultsReturned',
                          'firstResult',
                          'lastResult' );

        return array_intersect_key( $meta, array_flip($pagKeys) );
    }

    function getColumns()
    {
        $meta = $this->getMetaData();
        return isset( $meta['columns'] ) ? $meta['columns'] : null;
    }

    protected function _getColumnNames()
    {
        $columns  = $this->getColumns();
        $names    = array();

        foreach ( $columns as $_col ) {
            $names[] = $_col['name'];
        }

        return $names;
    }
}
