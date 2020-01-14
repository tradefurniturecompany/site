<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Status\Message\Patch;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Post\AbstractPost
{
    public function getFunction()
    {
        return $this->getMethod(). " workflow-integration-service/order-status";
    }

    public function getAction()
    {
        return sprintf( '/2.0.0/%s/workflow-integration-service/order/%s',
                        $this->getTransaction()->getAccountCode(),
                        self::encodeParam( $this->getTransaction()->getOrderIncrementId() ) );
    }

    public function getMethod()
    {
        return 'PATCH';
    }

    public function getBody()
    {
        return $this->_encodeJson( $this->getTransaction()->getOrderStatus() );
    }

    public function validate()
    {
        return $this
            ->_assertNotEmpty($this->getTransaction()->getOrderStatus(), 'orderStatus')
            ->_assertNotEmpty($this->getTransaction()->getOrderIncrementId(), 'orderIncrementId');
    }
}