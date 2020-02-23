<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Goodsout;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Get\AbstractGet
{
    protected $idOrderSet;

    function setOrderIdSet( $idOrderSet )
    {
        $this->idOrderSet = $idOrderSet;
        return $this;
    }

    function getOrderIdSet()
    {
        return $this->idOrderSet;
    }
    function getName()
    {
        return 'Goods-out Method GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Goodsout\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Goodsout\Get\Response';
    }

}