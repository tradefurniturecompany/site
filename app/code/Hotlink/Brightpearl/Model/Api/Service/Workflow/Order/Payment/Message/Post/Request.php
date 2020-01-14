<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Payment\Message\Post;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Post\AbstractPost
{
    public function getAction()
    {
        $transaction = $this->getTransaction();
        return sprintf( '/2.0.0/%s/workflow-integration-service/order/%s/payments',
                        $transaction->getAccountCode(),
                        self::encodeParam( $transaction->getOrderIncrementId() ) );
    }

    public function getFunction()
    {
        return $this->getMethod(). " workflow-integration-service/order/payments";
    }

    public function getBody()
    {
        return $this->_encodeJson( $this->getTransaction()->getPayment() );
    }

    public function validate()
    {
        return $this
            ->_assertNotEmpty($this->getTransaction()->getPayment(), 'payment')
            ->_assertNotEmpty($this->getTransaction()->getOrderIncrementId(), 'orderIncrementId');
    }
}