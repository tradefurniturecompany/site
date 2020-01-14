<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Accounting\Message\Customer\Payment\Post;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Post\AbstractPost
{

    public function getFunction()
    {
        return $this->getMethod(). " accounting-service/customer-payment";
    }

    public function getAction()
    {
        // TODO: replace hardcoded api version
        return sprintf( '/public-api/%s/accounting-service/customer-payment/',
                        $this->getTransaction()->getAccountCode() );
    }

    public function validate()
    {
        return $this->_assertNotEmpty( $this->getTransaction()->getRefund(), 'refund' );
    }

    public function getBody()
    {
        return $this->jsonHelper()->jsonEncode( $this->getTransaction()->getRefund() );
    }

}