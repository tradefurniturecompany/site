<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Accounting\Transaction\Customer\Payment;

class Post extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{

    protected $_refund = null;

    public function getName()
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

    public function setRefund( $data )
    {
        $this->_refund = $data;
        return $this;
    }

    public function getRefund()
    {
        return $this->_refund;
    }

}