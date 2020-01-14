<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Goodsout;

class Search extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Search\AbstractSearch
{
    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Goodsout\Search\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Goodsout\Search\Response';
    }
}