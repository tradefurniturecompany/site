<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Accounting\Message\Nominal\Code\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    function getCodes()
    {
        return $this->_get('response');
    }
}