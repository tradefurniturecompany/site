<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Message\Price\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    public function getPricelists()
    {
        return $this->_get('response');
    }
}