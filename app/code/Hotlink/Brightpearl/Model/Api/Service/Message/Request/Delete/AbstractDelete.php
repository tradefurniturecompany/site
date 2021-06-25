<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Message\Request\Delete;

abstract class AbstractDelete extends \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest
{
    function getMethod()
    {
        return 'DELETE';
    }

    function getContentEncoding()
    {
        return self::ENCODING_JSON;
    }

    function getBody()
    {
        return null;
    }
}