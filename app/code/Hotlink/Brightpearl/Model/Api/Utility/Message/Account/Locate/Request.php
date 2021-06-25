<?php
namespace Hotlink\Brightpearl\Model\Api\Utility\Message\Account\Locate;

class Request extends \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest
{

    public function getFunction()
    {
        return "account-location";
    }

    public function getAction()
    {
        return sprintf('/developer-tools/%s/account-location/%s',
                       $this->getTransaction()->getDevRef(),
                       $this->getTransaction()->getAccountCode());
    }

    public function getMethod()
    {
        return \Zend_Http_Client::GET;
    }

    public function getBody()
    {
        return null;
    }

    public function getContentEncoding()
    {
        return self::ENCODING_JSON;
    }

    public function validate()
    {
        return $this
            ->_assertNotEmpty($this->getTransaction()->getDevRef(), 'devRef')
            ->_assertNotEmpty($this->getTransaction()->getAccountCode(), 'accountCode');
    }
}