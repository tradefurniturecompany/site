<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Accounting\Message\Customer\Payment\Post;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{

    public function getRefundId()
    {
        return $this->_get( "response" );
    }

}
