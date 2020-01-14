<?php
namespace Hotlink\Brightpearl\Model\Api\Authorisation\Message\Authorise;

class Request extends \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest
{

    public function getFunction()
    {
        return "exchange instance";
    }

    public function getAction()
    {
        return sprintf( '/%s/exchange/instance', $this->getTransaction()->getAccountCode() );
    }

    public function getMethod()
    {
        return 'POST';
    }

    public function getContentEncoding()
    {
        return \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest::ENCODING_JSON;
    }

    public function getBody()
    {
        return $this->_encodeJson( array( 'token' => $this->getTransaction()->getRequestToken() ) );
    }

    public function validate()
    {
        return $this
            ->_assertNotEmpty( $this->getTransaction()->getAccountCode(), 'accountCode' )
            ->_assertNotEmpty( $this->getTransaction()->getRequestToken(), 'requestToken' );
    }
}