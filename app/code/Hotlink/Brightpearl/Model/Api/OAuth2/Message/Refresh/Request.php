<?php
namespace Hotlink\Brightpearl\Model\Api\OAuth2\Message\Refresh;

class Request extends \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest
{

    function getFunction()
    {
        return "refresh token";
    }

    function getAction()
    {
        return sprintf( '/%s/oauth/token', $this->getTransaction()->getAccount() );
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
            [ 'grant_type'    => 'refresh_token',
              'refresh_token' => $this->getTransaction()->getRefreshToken(),
              'client_id'     => $this->getTransaction()->getClientId()
            ]
        );
    }

    function validate()
    {
        return $this
            ->_assertNotEmpty( $this->getTransaction()->getAccount(), 'account' );
    }

}