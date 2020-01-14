<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Message\Credit\Post;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Post\AbstractPost
{

    public function getFunction()
    {
        return $this->getMethod(). " order-service/sales-credit";
    }

    public function getAction()
    {
        // TODO: replace hardcoded api version
        return sprintf( '/public-api/%s/order-service/sales-credit/',
                        $this->getTransaction()->getAccountCode() );
    }

    public function validate()
    {
        return $this->_assertNotEmpty( $this->getTransaction()->getCredit(), 'credit' );
    }

    public function getBody()
    {
        return $this->jsonHelper()->jsonEncode( $this->getTransaction()->getCredit() );
    }

}