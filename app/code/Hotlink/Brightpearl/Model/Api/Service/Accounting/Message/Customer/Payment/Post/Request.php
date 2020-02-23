<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Accounting\Message\Customer\Payment\Post;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Post\AbstractPost
{

    function getFunction()
    {
        return $this->getMethod(). " accounting-service/customer-payment";
    }

    function getAction()
    {
        // TODO: replace hardcoded api version
        return sprintf( '/public-api/%s/accounting-service/customer-payment/',
                        $this->getTransaction()->getAccountCode() );
    }

    function validate()
    {
        return $this->_assertNotEmpty( $this->getTransaction()->getRefund(), 'refund' );
    }

    function getBody()
    {
        return $this->jsonHelper()->jsonEncode( $this->getTransaction()->getRefund() );
    }

}