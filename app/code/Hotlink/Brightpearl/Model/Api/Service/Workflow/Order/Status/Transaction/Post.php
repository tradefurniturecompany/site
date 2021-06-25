<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Status\Transaction;

class Post extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{

    protected $_orderStatus = null;
    protected $_orderIncrementId = null;

    public function getName()
    {
        return 'Order Status Export';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Status\Message\Patch\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Status\Message\Patch\Response';
    }

    public function setOrderStatus( $orderStatus )
    {
        $this->_orderStatus = $orderStatus;
        return $this;
    }

    public function getOrderStatus()
    {
        return $this->_orderStatus;
    }

    public function setOrderIncrementId( $orderIncrementId )
    {
        $this->_orderIncrementId = $orderIncrementId;
        return $this;
    }

    public function getOrderIncrementId()
    {
        return $this->_orderIncrementId;
    }

}