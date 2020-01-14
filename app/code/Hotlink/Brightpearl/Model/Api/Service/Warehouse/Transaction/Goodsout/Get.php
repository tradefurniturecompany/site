<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Goodsout;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Get\AbstractGet
{
    protected $idOrderSet;

    public function setOrderIdSet( $idOrderSet )
    {
        $this->idOrderSet = $idOrderSet;
        return $this;
    }

    public function getOrderIdSet()
    {
        return $this->idOrderSet;
    }
    public function getName()
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