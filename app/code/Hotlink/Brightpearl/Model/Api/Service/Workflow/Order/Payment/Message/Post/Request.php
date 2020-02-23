<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Payment\Message\Post;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Post\AbstractPost
{
    function getAction()
    {
        $transaction = $this->getTransaction();
        return sprintf( '/2.0.0/%s/workflow-integration-service/order/%s/payments',
                        $transaction->getAccountCode(),
                        self::encodeParam( $transaction->getOrderIncrementId() ) );
    }

    function getFunction()
    {
        return $this->getMethod(). " workflow-integration-service/order/payments";
    }

    function getBody()
    {
        return $this->_encodeJson( $this->getTransaction()->getPayment() );
    }

    function validate()
    {
        return $this
            ->_assertNotEmpty($this->getTransaction()->getPayment(), 'payment')
            ->_assertNotEmpty($this->getTransaction()->getOrderIncrementId(), 'orderIncrementId');
    }
}