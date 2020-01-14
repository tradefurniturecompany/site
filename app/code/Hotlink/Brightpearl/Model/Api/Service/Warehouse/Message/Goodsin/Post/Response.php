<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Goodsin\Post;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{

    public function getGoodsinNoteId()
    {
        return $this->_get( "response" );
    }

}
