<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Message\Credit\Post;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{

    function getSalesCreditId()
    {
        return $this->_get( "response" );
    }

}
