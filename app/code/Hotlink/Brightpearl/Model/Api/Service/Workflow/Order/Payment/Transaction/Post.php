<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Payment\Transaction;

class Post extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{
    protected $_payment = null;
    protected $_orderIncrementId = null;

    public function getName()
    {
        return 'Order Payment Export';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Payment\Message\Post\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Payment\Message\Post\Response';
    }

    public function setPayment( $payment )
    {
        $this->_payment = $payment;
        return $this;
    }

    public function getPayment()
    {
        return $this->_payment;
    }

    public function setOrderIncrementId( $id )
    {
        $this->_orderIncrementId = $id;
        return $this;
    }

    public function getOrderIncrementId()
    {
        return $this->_orderIncrementId;
    }
}