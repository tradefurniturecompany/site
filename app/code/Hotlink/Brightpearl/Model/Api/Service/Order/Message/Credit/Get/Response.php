<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Message\Credit\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{

    public function getSalesCredits()
    {
        $result = $this->_get( 'response' );
        return ( $result ) ? $result : [];
    }

}