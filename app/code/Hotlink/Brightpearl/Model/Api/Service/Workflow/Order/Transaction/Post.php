<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Transaction;

class Post extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{

    protected $_order = null;

    function getName()
    {
        return 'Order Export';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Message\Post\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Message\Post\Response';
    }

    function setOrder( $order )
    {
        $this->_order = $order;
        return $this;
    }

    function getOrder()
    {
        return $this->_order;
    }

}