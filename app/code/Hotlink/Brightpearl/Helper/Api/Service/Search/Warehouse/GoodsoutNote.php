<?php
namespace Hotlink\Brightpearl\Helper\Api\Service\Search\Warehouse;

class GoodsoutNote extends \Hotlink\Brightpearl\Helper\Api\Service\Search\AbstractSearch
{
    public function getName()
    {
        return 'Warehouse GoodsoutNote Search API';
    }

    protected function _getTransactionModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Goodsout\Search';
    }

    protected function _getPlatformDataModel()
    {
        return '\Hotlink\Brightpearl\Model\Platform\Data';
    }
}