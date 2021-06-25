<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Message\Status\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    function getStatuses()
    {
        return $this->_get('response');
    }
}