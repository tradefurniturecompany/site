<?php
namespace Hotlink\Brightpearl\Helper\Api\Service\Search;

class Order extends \Hotlink\Brightpearl\Helper\Api\Service\Search\AbstractSearch
{
    function getName()
    {
        return 'Order Search API';
    }

    protected function _getTransactionModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Order\Transaction\Search';
    }

    protected function _getPlatformDataModel()
    {
        return '\Hotlink\Brightpearl\Model\Platform\Data';
    }
}