<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get;

abstract class AbstractGet extends \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest
{
    function getMethod()
    {
        return 'GET';
    }

    function getContentEncoding()
    {
        return null;
    }

    function getBody()
    {
        return null;
    }

    function validate()
    {
        return $this->_assertNotEmpty( $this->getTransaction()->getAccountCode(), 'accountCode' );
    }
}