<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Message\Request\Search;

abstract class AbstractSearch extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{
    const SORT_ASC  = 'ASC';
    const SORT_DESC = 'DESC';
    const PERIOD_SEPARATOR = '/';

    abstract protected function _getAction();

    function getAction()
    {
        $action = $this->_getAction();
        $query  = $this->_buildQuery();

        return $action . '?' . $query;
    }

    protected function _buildQuery()
    {
        $transaction = $this->getTransaction();
        $params      = array();


        if ($filters = $transaction->getFilters()) {
            $params = array_merge($params, $filters);
        }
        if ($sortBy = $transaction->getSortBy()) {
            $direction = ($transaction->getSortDirection() ? $transaction->getSortDirection() : self::SORT_DESC);
            $params['sort'] = $sortBy . '|' . $direction;
        }
        if ($pageSize = $transaction->getPageSize()) {
            $params['pageSize'] = $pageSize;
        }
        if ($firstResult = $transaction->getFirstResult()) {
            $params['firstResult'] = $firstResult;
        }
        if ($columns = $transaction->getColumns()) {
            $params['columns'] = $this->_csv($columns);
        }

        return http_build_query($params);
    }

    function validate()
    {
        $transaction = $this->getTransaction();

        if ($sortBy = $transaction->getSortBy() and $direction = $transaction->getSortDirection()) {
            $available = array(self::SORT_ASC, self::SORT_DESC);
            if (!in_array($direction, $available)) {
                $expected = implode(",", $available);
                $this->getExceptionHelper()->throwValidation('Invalid sort direction ['.$direction.'], expected ['.$expected.'].');
            }
        }
        if ($pageSize = $transaction->getPageSize()) {
            $this->_assertNumeric($pageSize, 'pageSize');
        }
        if ($firstResult = $transaction->getFirstResult()) {
            $this->_assertNumeric($firstResult, 'firstResult');
        }
    }

    protected function _assertPeriod($period, $name)
    {
        // 1. single date 2010-12-16
        $pos = strpos( $period, self::PERIOD_SEPARATOR );
        if ( $pos === false ) {
            return $this->_assertDatetime( $period, $name );
        }

        // only one separator is accepted
        if ( substr_count( $period, self::PERIOD_SEPARATOR ) > 1) {
            $this->getExceptionHelper()->throwValidation( 'Invalid period format "' . $period . '".' );
        }

        // 2. before date /2010-12-16
        if ($pos === 0) {
            return $this->_assertDatetime( substr($period, 1), $name );
        }

        // 3. after date 2010-12-16/
        if ($pos === strlen($period) - 1) {
            return $this->_assertDatetime( substr($period, 0, -1), $name );
        }

        // 4. between dates 2010-12-10/2010-12-16
        $this->_assertDatetime( substr($period, 0, $pos), $name );
        $this->_assertDatetime( substr($period, $pos, strlen($period)), $name );
    }
}
