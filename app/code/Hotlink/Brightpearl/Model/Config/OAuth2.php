<?php
namespace Hotlink\Brightpearl\Model\Config;

class OAuth2 extends \Hotlink\Brightpearl\Model\Config\AbstractConfig
{

    protected function _getGroup()
    {
        return 'oauth2';
    }

    public function getProxyUrl( $storeId = null )
    {
        return $this->getConfigData( 'proxy_url', $storeId, null);
    }

    public function isActive()
    {
        return $this->getAccount()
            && $this->getAccessToken()
            && $this->getInstallationInstanceId()
            && $this->getApiDomain()
            ? true
            : false;
    }

    //
    //  otp
    //
    public function getOtp( $storeId = null )
    {
        return $this->getConfigData( 'otp', $storeId, null);
    }

    public function saveOtp( $value, $storeId = null )
    {
        $this->saveValue( $value, 'otp', $storeId );
        return $this->setConfigData( 'otp', $storeId, $value );
    }

    //
    //  otp_success_url
    //
    public function getOtpSuccessUrl( $storeId = null )
    {
        return $this->getConfigData( 'otp_success_url', $storeId, null);
    }

    public function saveOtpSuccessUrl( $value, $storeId = null )
    {
        $this->saveValue( $value, 'otp_success_url', $storeId );
        return $this->setConfigData( 'otp_success_url', $storeId, $value );
    }

    //
    //  otp_callback_url
    //
    public function getOtpCallbackUrl( $storeId = null )
    {
        return $this->getConfigData( 'otp_callback_url', $storeId, null);
    }

    public function saveOtpCallbackUrl( $value, $storeId = null )
    {
        $this->saveValue( $value, 'otp_callback_url', $storeId );
        return $this->setConfigData( 'otp_callback_url', $storeId, $value );
    }

    //
    //  otp_timestamp
    //
    public function getOtpTimestamp( $storeId = null )
    {
        return $this->getConfigData( 'otp_timestamp', $storeId, null);
    }

    public function saveOtpTimestamp( $value, $storeId = null )
    {
        $this->saveValue( $value, 'otp_timestamp', $storeId );
        return $this->setConfigData( 'otp_timestamp', $storeId, $value );
    }

    //
    //  account
    //
    public function getAccount( $storeId = null )
    {
        return $this->getConfigData( 'account', $storeId, null);
    }

    public function saveAccount( $value, $storeId = null )
    {
        $this->saveValue( $value, 'account', $storeId );
        return $this->setConfigData( 'account', $storeId, $value );
    }

    //
    //  access_token
    //
    public function getAccessToken( $storeId = null )
    {
        return $this->getConfigData( 'access_token', $storeId, null);
    }

    public function saveAccessToken( $value, $storeId = null )
    {
        $this->saveValue( $value, 'access_token', $storeId );
        return $this->setConfigData( 'access_token', $storeId, $value );
    }

    //
    //  expires_in
    //
    public function getExpiresIn( $storeId = null )
    {
        return $this->getConfigData( 'expires_in', $storeId, null);
    }

    public function saveExpiresIn( $value, $storeId = null )
    {
        $this->saveValue( $value, 'expires_in', $storeId );
        return $this->setConfigData( 'expires_in', $storeId, $value );
    }

    //
    //  refresh_token
    //
    public function getRefreshToken( $storeId = null )
    {
        return $this->getConfigData( 'refresh_token', $storeId, null);
    }

    public function saveRefreshToken( $value, $storeId = null )
    {
        $this->saveValue( $value, 'refresh_token', $storeId );
        return $this->setConfigData( 'refresh_token', $storeId, $value );
    }

    //
    //  installation_instance_id
    //
    public function getInstallationInstanceId( $storeId = null )
    {
        return $this->getConfigData( 'installation_instance_id', $storeId, null);
    }

    public function saveInstallationInstanceId( $value, $storeId = null )
    {
        $this->saveValue( $value, 'installation_instance_id', $storeId );
        return $this->setConfigData( 'installation_instance_id', $storeId, $value );
    }

    //
    //  api_domain
    //
    public function getApiDomain( $storeId = null )
    {
        return $this->getConfigData( 'api_domain', $storeId, null);
    }

    public function saveApiDomain( $value, $storeId = null )
    {
        $this->saveValue( $value, 'api_domain', $storeId );
        return $this->setConfigData( 'api_domain', $storeId, $value );
    }

}
