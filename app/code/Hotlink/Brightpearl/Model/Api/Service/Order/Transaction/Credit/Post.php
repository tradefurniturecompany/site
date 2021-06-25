<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Transaction\Credit;

class Post extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{

    protected $_credit = null;

    public function getName()
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

    public function setCredit( $data )
    {
        $this->_credit = $data;
        return $this;
    }

    public function getCredit()
    {
        return $this->_credit;
    }

}