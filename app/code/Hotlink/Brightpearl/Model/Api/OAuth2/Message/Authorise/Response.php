<?php
namespace Hotlink\Brightpearl\Model\Api\OAuth2\Message\Authorise;

class Response extends \Hotlink\Brightpearl\Model\Api\Message\Response\AbstractResponse
{

    protected function _validate()
    {
        if ( !$this->getAccessToken() )
            {
                $this->getExceptionHelper()->throwValidation( 'Missing required field [access_token] in response' );
            }
        if ( !$this->getRefreshToken() )
            {
                $this->getExceptionHelper()->throwValidation( 'Missing required field [refresh_token] in response' );
            }
        if ( !$this->getTokenType() )
            {
                $this->getExceptionHelper()->throwValidation( 'Missing required field [token_type] in response' );
            }
        if ( !$this->getExpiresIn() )
            {
                $this->getExceptionHelper()->throwValidation( 'Missing required field [expires_in] in response' );
            }
        if ( !$this->getInstallationInstanceId() )
            {
                $this->getExceptionHelper()->throwValidation( 'Missing required field [installation_instance_id] in response' );
            }
        if ( !$this->getApiDomain() )
            {
                $this->getExceptionHelper()->throwValidation( 'Missing required field [api_domain] in response' );
            }
        return $this;
    }

    public function getAccessToken()
    {
        return $this->_get( 'access_token' );
    }

    public function getTokenType()
    {
        return $this->_get( 'token_type' );
    }

    public function getExpiresIn()
    {
        return $this->_get( 'expires_in' );
    }

    public function getRefreshToken()
    {
        return $this->_get( 'refresh_token' );
    }

    public function getInstallationInstanceId()
    {
        return $this->_get( 'installation_instance_id' );
    }

    public function getApiDomain()
    {
        return $this->_get( 'api_domain' );
    }

}