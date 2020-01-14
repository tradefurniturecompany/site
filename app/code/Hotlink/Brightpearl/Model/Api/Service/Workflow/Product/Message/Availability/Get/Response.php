<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Message\Availability\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    public function getAvailability()
    {
        return $this->_get('response');
    }
}