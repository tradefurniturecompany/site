<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Webhook\Post;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    function getWebhookId()
    {
        return $this->_get('response');
    }
}