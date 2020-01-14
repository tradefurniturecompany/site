<?php
namespace Hotlink\Brightpearl\Model\Config;

class Authorisation extends \Hotlink\Brightpearl\Model\Config\AbstractConfig
{

    protected function _getGroup()
    {
        return 'authorisation';
    }

    public function getAccountCode($storeId = null)
    {
        return $this->getConfigData( 'accountCode', $storeId, null);
    }

    public function getApiDomain($storeId = null)
    {
        return $this->getConfigData( 'apiDomain', $storeId, null);
    }

    public function getLocationUrl($storeId = null)
    {
        return $this->getConfigData( 'locationUrl', $storeId, null);
    }

    public function getOtp($storeId = null)
    {
        return $this->getConfigData( 'otp', $storeId, null);
    }

    public function getToken($storeId = null)
    {
        $encrypted = $this->getConfigData( 'token', $storeId, null );

        return $this->encryptor->decrypt( $encrypted );
    }

    public function getTokenTimestamp($storeId = null)
    {
        return $this->getConfigData( 'tokenTimestamp', $storeId, null );
    }

    public function saveToken($token, $storeId = null)
    {
        $encrypted = $this->encryptor->encrypt( $token );

        return $this->saveValue($encrypted, 'token', $storeId);
    }

    public function saveTokenTimestamp($timestamp, $storeId = null)
    {
        return $this->saveValue($timestamp, 'tokenTimestamp', $storeId);
    }

    public function saveOtp($otp, $storeId = null)
    {
        return $this->saveValue($otp, 'otp', $storeId);
    }

    public function saveAccountCode($accountCode, $storeId = null)
    {
        return $this->saveValue($accountCode, 'accountCode', $storeId);
    }

    public function saveApiDomain($domain, $storeId = null)
    {
        return $this->saveValue($domain, 'apiDomain', $storeId);
    }


    public function getCurlValidateHost( $storeId = null )
    {
        return $this->getConfigData( 'curl_validate_host', $storeId, null );
    }

    public function getCurlValidateCertificate( $storeId = null )
    {
        return $this->getConfigData( 'curl_validate_certificate', $storeId, null );
    }


    public function getHttpAuthEnabled( $storeId = null )
    {
        return $this->getConfigData( 'http_auth_enabled', $storeId, null );
    }

    public function getHttpAuthUsername( $storeId = null )
    {
        return $this->getConfigData( 'http_auth_username', $storeId, null );
    }

    public function getHttpAuthPassword( $storeId = null )
    {
        return $this->getConfigData( 'http_auth_password', $storeId, null );
    }

}
