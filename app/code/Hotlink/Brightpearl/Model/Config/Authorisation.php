<?php
namespace Hotlink\Brightpearl\Model\Config;

class Authorisation extends \Hotlink\Brightpearl\Model\Config\AbstractConfig
{

    protected function _getGroup()
    {
        return 'authorisation';
    }

    function getAccountCode($storeId = null)
    {
        return $this->getConfigData( 'accountCode', $storeId, null);
    }

    function getApiDomain($storeId = null)
    {
        return $this->getConfigData( 'apiDomain', $storeId, null);
    }

    function getLocationUrl($storeId = null)
    {
        return $this->getConfigData( 'locationUrl', $storeId, null);
    }

    function getOtp($storeId = null)
    {
        return $this->getConfigData( 'otp', $storeId, null);
    }

    function getToken($storeId = null)
    {
        $encrypted = $this->getConfigData( 'token', $storeId, null );

        return $this->encryptor->decrypt( $encrypted );
    }

    function getTokenTimestamp($storeId = null)
    {
        return $this->getConfigData( 'tokenTimestamp', $storeId, null );
    }

    function saveToken($token, $storeId = null)
    {
        $encrypted = $this->encryptor->encrypt( $token );

        return $this->saveValue($encrypted, 'token', $storeId);
    }

    function saveTokenTimestamp($timestamp, $storeId = null)
    {
        return $this->saveValue($timestamp, 'tokenTimestamp', $storeId);
    }

    function saveOtp($otp, $storeId = null)
    {
        return $this->saveValue($otp, 'otp', $storeId);
    }

    function saveAccountCode($accountCode, $storeId = null)
    {
        return $this->saveValue($accountCode, 'accountCode', $storeId);
    }

    function saveApiDomain($domain, $storeId = null)
    {
        return $this->saveValue($domain, 'apiDomain', $storeId);
    }


    function getCurlValidateHost( $storeId = null )
    {
        return $this->getConfigData( 'curl_validate_host', $storeId, null );
    }

    function getCurlValidateCertificate( $storeId = null )
    {
        return $this->getConfigData( 'curl_validate_certificate', $storeId, null );
    }


    function getHttpAuthEnabled( $storeId = null )
    {
        return $this->getConfigData( 'http_auth_enabled', $storeId, null );
    }

    function getHttpAuthUsername( $storeId = null )
    {
        return $this->getConfigData( 'http_auth_username', $storeId, null );
    }

    function getHttpAuthPassword( $storeId = null )
    {
        return $this->getConfigData( 'http_auth_password', $storeId, null );
    }

}
