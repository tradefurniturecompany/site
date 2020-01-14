<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Transaction;

class Search extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Search\AbstractSearch
{
    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Order\Message\Search\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Order\Message\Search\Response';
    }
}