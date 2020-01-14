<?php
namespace Hotlink\Brightpearl\Model\Api\OAuth2\Message\Refresh;

class Request extends \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest
{

    public function getFunction()
    {
        return "refresh token";
    }

    public function getAction()
    {
        return sprintf( '/%s/oauth/token', $this->getTransaction()->getAccount() );
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
            [ 'grant_type'    => 'refresh_token',
              'refresh_token' => $this->getTransaction()->getRefreshToken(),
              'client_id'     => $this->getTransaction()->getClientId()
            ]
        );
    }

    public function validate()
    {
        return $this
            ->_assertNotEmpty( $this->getTransaction()->getAccount(), 'account' );
    }

}