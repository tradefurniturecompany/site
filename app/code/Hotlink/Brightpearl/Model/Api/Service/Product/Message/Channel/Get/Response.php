<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Product\Message\Channel\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    public function getChannels()
    {
        return $this->_get('response');
    }
}