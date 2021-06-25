<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Dropship;

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
        return 'Drop-ship Method GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Dropship\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Dropship\Get\Response';
    }

}
