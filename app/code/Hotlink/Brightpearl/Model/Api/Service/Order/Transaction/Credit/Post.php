<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Transaction\Credit;

class Post extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{

    protected $_credit = null;

    function getName()
    {
        return 'Creditmemo Export';
    }

    protected function _getRequestModel()
    {
        return 'Hotlink\Brightpearl\Model\Api\Service\Order\Message\Credit\Post\Request';
    }

    protected function _getResponseModel()
    {
        return 'Hotlink\Brightpearl\Model\Api\Service\Order\Message\Credit\Post\Response';
    }

    function setCredit( $data )
    {
        $this->_credit = $data;
        return $this;
    }

    function getCredit()
    {
        return $this->_credit;
    }

}