<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Message\Field\Metadata\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    public function getFieldsMetadata()
    {
        return $this->_get('response');
    }
}