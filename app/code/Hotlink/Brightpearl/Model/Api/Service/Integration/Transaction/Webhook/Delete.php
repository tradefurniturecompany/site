<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Transaction\Webhook;

class Delete extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{
    protected $webhookId;

    function getName()
    {
        return 'Webhook DELETE';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Webhook\Delete\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Webhook\Delete\Response';
    }

    function setWebhookId($webhookId)
    {
        $this->webhookId = $webhookId;
        return $this;
    }

    function getWebhookId()
    {
        return $this->webhookId;
    }
}