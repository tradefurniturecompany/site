<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get;

abstract class AbstractGet extends \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest
{
    public function getMethod()
    {
        return 'GET';
    }

    public function getContentEncoding()
    {
        return self::ENCODING_JSON;
    }

    public function getBody()
    {
        return null;
    }

    public function validate()
    {
        return $this->_assertNotEmpty( $this->getTransaction()->getAccountCode(), 'accountCode' );
    }
}