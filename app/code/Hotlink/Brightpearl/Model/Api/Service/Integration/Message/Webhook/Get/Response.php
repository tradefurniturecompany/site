<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Webhook\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    function getWebhooks()
    {
        return $this->_get('response');
    }
}