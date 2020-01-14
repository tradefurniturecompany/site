<?php
namespace Hotlink\Brightpearl\Model\Api\OAuth2\Message\Authorise;

class Request extends \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest
{

    public function getFunction()
    {
        return "exchange instance";
    }

    public function getAction()
    {
        return sprintf( '/exchange/%s', $this->getTransaction()->getAccount() );
    }

    public function getMethod()
    {
        return 'POST';
    }

    public function getContentEncoding()
    {
        return \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest::ENCODING_URLENCODED;
    }

    public function getBody()
    {
        return $this->_encodeUrlencoded(
            [ 'grant_type'   => 'authorization_code',
              'code'         => $this->getTransaction()->getCode(),
              'redirect_uri' => $this->getTransaction()->getRedirectUri(),
              'client_id'    => $this->getTransaction()->getClientId()
            ]
        );
    }

    public function validate()
    {
        return $this
            ->_assertNotEmpty( $this->getTransaction()->getAccount(), 'account' )
            ->_assertNotEmpty( $this->getTransaction()->getCode(), 'code' )
            ->_assertNotEmpty( $this->getTransaction()->getRedirectUri(), 'redirectUri' )
            ->_assertNotEmpty( $this->getTransaction()->getClientId(), 'clientId' );
    }

}