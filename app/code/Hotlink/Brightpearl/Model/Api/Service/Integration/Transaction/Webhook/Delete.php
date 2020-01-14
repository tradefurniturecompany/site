<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Transaction\Webhook;

class Delete extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{
    protected $webhookId;

    public function getName()
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

    public function setWebhookId($webhookId)
    {
        $this->webhookId = $webhookId;
        return $this;
    }

    public function getWebhookId()
    {
        return $this->webhookId;
    }
}