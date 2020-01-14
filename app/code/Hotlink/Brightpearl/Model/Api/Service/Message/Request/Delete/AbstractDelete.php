<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Message\Request\Delete;

abstract class AbstractDelete extends \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest
{
    public function getMethod()
    {
        return 'DELETE';
    }

    public function getContentEncoding()
    {
        return null;
    }

    public function getBody()
    {
        return null;
    }
}