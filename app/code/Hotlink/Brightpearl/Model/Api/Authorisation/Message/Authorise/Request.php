<?php
namespace Hotlink\Brightpearl\Model\Api\Authorisation\Message\Authorise;

class Request extends \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest
{

    function getFunction()
    {
        return "exchange instance";
    }

    function getAction()
    {
        return sprintf( '/%s/exchange/instance', $this->getTransaction()->getAccountCode() );
    }

    function getMethod()
    {
        return 'POST';
    }

    function getContentEncoding()
    {
        return \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest::ENCODING_JSON;
    }

    function getBody()
    {
        return $this->_encodeJson( array( 'token' => $this->getTransaction()->getRequestToken() ) );
    }

    function validate()
    {
        return $this
            ->_assertNotEmpty( $this->getTransaction()->getAccountCode(), 'accountCode' )
            ->_assertNotEmpty( $this->getTransaction()->getRequestToken(), 'requestToken' );
    }
}