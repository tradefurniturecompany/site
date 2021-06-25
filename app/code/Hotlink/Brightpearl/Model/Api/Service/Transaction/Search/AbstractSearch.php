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


    public function setColumns($columns)
    {
        $this->_columns = $columns;
        return $this;
    }

    public function getColumns()
    {
        return $this->_columns;
    }

    public function addFilter($name, $value)
    {
        if (!$this->_filters) {
            $this->_filters = array();
        }

        $this->_filters[$name] = $value;

        return $this;
    }

    public function setFilters($filters)
    {
        $this->_filters = $filters;
        return $this;
    }

    public function getFilters()
    {
        return $this->_filters;
    }

    public function setPageSize($pageSize)
    {
        $this->_pageSize = (int) $pageSize;
        return $this;
    }

    public function getPageSize()
    {
        return $this->_pageSize;
    }

    public function setFirstResult($firstResult)
    {
        $this->_firstResult = (int) $firstResult;
        return $this;
    }

    public function getFirstResult()
    {
        return $this->_firstResult;
    }

    public function setSortBy($name)
    {
        $this->_sortBy = $name;
        return $this;
    }

    public function getSortBy()
    {
        return $this->_sortBy;
    }

    public function setSortDirection($direction)
    {
        $this->_sortDirection = $direction;
        return $this;
    }

    public function getSortDirection()
    {
        return $this->_sortDirection;
    }


}
