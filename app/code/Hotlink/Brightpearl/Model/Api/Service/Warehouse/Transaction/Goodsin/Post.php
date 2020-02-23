<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Goodsin;

class Post extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{

    protected $_purchaseOrderId = null;
    protected $_note = null;

    function getName()
    {
        return 'Goods-in Note Create';
    }

    protected function _getRequestModel()
    {
        return 'Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Goodsin\Post\Request';
    }

    protected function _getResponseModel()
    {
        return 'Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Goodsin\Post\Response';
    }

    function setPurchaseOrderId( $value )
    {
        $this->_purchaseOrderId = $value;
        return $this;
    }

    function getPurchaseOrderId()
    {
        return $this->_purchaseOrderId;
    }

    function setNote( $data )
    {
        $this->_note = $data;
        return $this;
    }

    function getNote()
    {
        return $this->_note;
    }

}