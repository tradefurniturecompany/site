<?php
namespace Hotlink\Brightpearl\Model\Api\OAuth2\Message\Authorise;

class Request extends \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest
{

    function getFunction()
    {
        return "exchange instance";
    }

    function getAction()
    {
        return sprintf( '/exchange/%s', $this->getTransaction()->getAccount() );
    }

    function getMethod()
    {
        return 'POST';
    }

    function getContentEncoding()
    {
        return \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest::ENCODING_URLENCODED;
    }

    function getBody()
    {
        return $this->_encodeUrlencoded(
            [ 'grant_type'   => 'authorization_code',
              'code'         => $this->getTransaction()->getCode(),
              'redirect_uri' => $this->getTransaction()->getRedirectUri(),
              'client_id'    => $this->getTransaction()->getClientId()
            ]
        );
    }

    function validate()
    {
        return $this
            ->_assertNotEmpty( $this->getTransaction()->getAccount(), 'account' )
            ->_assertNotEmpty( $this->getTransaction()->getCode(), 'code' )
            ->_assertNotEmpty( $this->getTransaction()->getRedirectUri(), 'redirectUri' )
            ->_assertNotEmpty( $this->getTransaction()->getClientId(), 'clientId' );
    }

}