<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Message\Request\Post;

abstract class AbstractPost extends \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest
{

    public function getMethod()
    {
        return 'POST';
    }

    public function getContentEncoding()
    {
        return \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest::ENCODING_JSON;
    }
}