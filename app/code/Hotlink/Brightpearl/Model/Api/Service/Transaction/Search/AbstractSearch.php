<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Transaction\Search;

abstract class AbstractSearch extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{
    // columns
    protected $_columns;

    // filter
    protected $_filters;

    // pagination
    protected $_pageSize;     // the maximum number of results that will be returned in a response.
    protected $_firstResult;  // index of the first result you would like in your results.

    // sorting
    protected $_sortBy;
    protected $_sortDirection;


    function setColumns($columns)
    {
        $this->_columns = $columns;
        return $this;
    }

    function getColumns()
    {
        return $this->_columns;
    }

    function addFilter($name, $value)
    {
        if (!$this->_filters) {
            $this->_filters = array();
        }

        $this->_filters[$name] = $value;

        return $this;
    }

    function setFilters($filters)
    {
        $this->_filters = $filters;
        return $this;
    }

    function getFilters()
    {
        return $this->_filters;
    }

    function setPageSize($pageSize)
    {
        $this->_pageSize = (int) $pageSize;
        return $this;
    }

    function getPageSize()
    {
        return $this->_pageSize;
    }

    function setFirstResult($firstResult)
    {
        $this->_firstResult = (int) $firstResult;
        return $this;
    }

    function getFirstResult()
    {
        return $this->_firstResult;
    }

    function setSortBy($name)
    {
        $this->_sortBy = $name;
        return $this;
    }

    function getSortBy()
    {
        return $this->_sortBy;
    }

    function setSortDirection($direction)
    {
        $this->_sortDirection = $direction;
        return $this;
    }

    function getSortDirection()
    {
        return $this->_sortDirection;
    }


}
