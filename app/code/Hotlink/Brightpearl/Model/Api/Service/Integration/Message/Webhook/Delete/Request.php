<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Webhook\Delete;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Delete\AbstractDelete
{
    function getFunction()
    {
        return $this->getMethod() ." integration-service/webhook";
    }

    function getAction()
    {
        return sprintf( '/public-api/%s/integration-service/webhook/%s',
                        $this->getTransaction()->getAccountCode(),
                        $this->getTransaction()->getWebhookId() );
    }

    function validate()
    {
        return $this->_assertNotEmpty( $this->getTransaction()->getWebhookId(), 'webhookId' );
    }
}