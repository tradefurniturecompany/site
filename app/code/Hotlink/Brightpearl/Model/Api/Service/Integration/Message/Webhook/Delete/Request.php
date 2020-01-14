<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Webhook\Delete;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Delete\AbstractDelete
{
    public function getFunction()
    {
        return $this->getMethod() ." integration-service/webhook";
    }

    public function getAction()
    {
        return sprintf( '/public-api/%s/integration-service/webhook/%s',
                        $this->getTransaction()->getAccountCode(),
                        $this->getTransaction()->getWebhookId() );
    }

    public function validate()
    {
        return $this->_assertNotEmpty( $this->getTransaction()->getWebhookId(), 'webhookId' );
    }
}