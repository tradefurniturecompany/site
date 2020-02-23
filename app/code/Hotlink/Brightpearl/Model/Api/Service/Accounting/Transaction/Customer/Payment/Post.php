<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Accounting\Transaction\Customer\Payment;

class Post extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{

    protected $_refund = null;

    function getName()
    {
        return 'Refund Export';
    }

    protected function _getRequestModel()
    {
        return 'Hotlink\Brightpearl\Model\Api\Service\Accounting\Message\Customer\Payment\Post\Request';
    }

    protected function _getResponseModel()
    {
        return 'Hotlink\Brightpearl\Model\Api\Service\Accounting\Message\Customer\Payment\Post\Response';
    }

    function setRefund( $data )
    {
        $this->_refund = $data;
        return $this;
    }

    function getRefund()
    {
        return $this->_refund;
    }

}