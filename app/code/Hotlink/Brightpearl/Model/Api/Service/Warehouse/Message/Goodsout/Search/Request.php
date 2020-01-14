<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Goodsout\Search;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Search\AbstractSearch
{
    const COLUMN_CREATED_ON   = 'createdOn';
    const COLUMN_RELEASE_DATE = 'releaseDate';

    public function getFunction()
    {
        return $this->getMethod(). " /goods-note/goods-out-search";
    }

    public function _getAction()
    {
        $accountCode = $this->getTransaction()->getAccountCode();

        return sprintf( '/public-api/%s/warehouse-service/goods-note/goods-out-search', $accountCode );
    }

    public function validate()
    {
        parent::validate();

        $transaction = $this->getTransaction();

        // validate filters
        if ($filters = $transaction->getFilters()) {
            foreach ($filters as $_name => $_value) {

                switch ($_name) {

                case self::COLUMN_CREATED_ON:
                case self::COLUMN_RELEASE_DATE:
                    $this->_assertPeriod($_value, $_name);
                break;

                }
            }
        }

        // validate columns
    }
}