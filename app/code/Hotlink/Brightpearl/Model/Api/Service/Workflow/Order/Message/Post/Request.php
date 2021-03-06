<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Message\Post;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Post\AbstractPost
{
    function getFunction()
    {
        return $this->getMethod(). " workflow-integration-service/order";
    }

    function getAction()
    {
        return sprintf( '/2.0.0/%s/workflow-integration-service/order', $this->getTransaction()->getAccountCode() );
    }

    function getBody()
    {
        return $this->_encodeJson( $this->getTransaction()->getOrder() );
    }

    function validate()
    {
        return $this->_assertNotEmpty($this->getTransaction()->getOrder(), 'order');
    }
}