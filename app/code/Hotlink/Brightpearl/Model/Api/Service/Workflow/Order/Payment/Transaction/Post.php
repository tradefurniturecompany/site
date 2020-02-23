<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Payment\Transaction;

class Post extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{
    protected $_payment = null;
    protected $_orderIncrementId = null;

    function getName()
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

    function setPayment( $payment )
    {
        $this->_payment = $payment;
        return $this;
    }

    function getPayment()
    {
        return $this->_payment;
    }

    function setOrderIncrementId( $id )
    {
        $this->_orderIncrementId = $id;
        return $this;
    }

    function getOrderIncrementId()
    {
        return $this->_orderIncrementId;
    }
}