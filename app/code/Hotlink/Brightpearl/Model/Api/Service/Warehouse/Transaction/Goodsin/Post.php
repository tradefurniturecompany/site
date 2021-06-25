<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Goodsin;

class Post extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{

    protected $_purchaseOrderId = null;
    protected $_note = null;

    public function getName()
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

    public function setPurchaseOrderId( $value )
    {
        $this->_purchaseOrderId = $value;
        return $this;
    }

    public function getPurchaseOrderId()
    {
        return $this->_purchaseOrderId;
    }

    public function setNote( $data )
    {
        $this->_note = $data;
        return $this;
    }

    public function getNote()
    {
        return $this->_note;
    }

}