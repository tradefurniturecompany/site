<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Message\Search;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Search\AbstractSearch
{
    const COLUMN_PLACED_ON     = 'placedOn';
    const COLUMN_DELIVERY_DATE = 'deliveryDate';
    const COLUMN_UPDATED_ON    = 'updatedOn';

    public function getFunction()
    {
        return $this->getMethod(). " order-service/order-search";
    }

    public function _getAction()
    {
        $accountCode = $this->getTransaction()->getAccountCode();

        return sprintf('/public-api/%s/order-service/order-search', $accountCode);
    }

    public function validate()
    {
        parent::validate();

        $transaction = $this->getTransaction();

        // validate filters
        if ( $filters = $transaction->getFilters() ) {
            foreach ( $filters as $_name => $_value ) {

                switch ($_name) {

                case self::COLUMN_PLACED_ON:
                case self::COLUMN_DELIVERY_DATE:
                case self::COLUMN_UPDATED_ON:
                    $this->_assertPeriod( $_value, $_name );
                break;

                }
            }
        }
    }

}
