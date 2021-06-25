<?php
namespace Hotlink\Brightpearl\Model\Api\Utility\Message\Account\Locate;

class Request extends \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest
{

    function getFunction()
    {
        return "account-location";
    }

    function getAction()
    {
        return sprintf('/developer-tools/%s/account-location/%s',
                       $this->getTransaction()->getDevRef(),
                       $this->getTransaction()->getAccountCode());
    }

    function getMethod()
    {
        return \Zend_Http_Client::GET;
    }

    function getBody()
    {
        return null;
    }

    function getContentEncoding()
    {
        return self::ENCODING_JSON;
    }

    function validate()
    {
        return $this
            ->_assertNotEmpty($this->getTransaction()->getDevRef(), 'devRef')
            ->_assertNotEmpty($this->getTransaction()->getAccountCode(), 'accountCode');
    }
}