<?php
namespace Hotlink\Brightpearl\Helper\Api\OAuth2;

class Otp
{

    protected $oauth2config;
    
    function __construct( \Hotlink\Brightpearl\Model\Config\OAuth2 $oauth2config )
    {
        $this->oauth2config = $oauth2config;
    }

    function get()
    {
        return $this->oauth2config->getOtp();
    }

    function getSuccessUrl()
    {
        return $this->oauth2config->getOtpSuccessUrl();
    }

    function getCallbackUrl()
    {
        return $this->oauth2config->getOtpCallbackUrl();
    }

    function isExpired( $timeoutSeconds = 5 * 60 )
    {
        $otp = $this->oauth2config->getOtp();
        $timestamp = $this->oauth2config->getOtpTimestamp();

        if ( ( ! $otp ) || ( ! $timestamp ) )
            {
                return true;
            }
        $now = microtime( true );
        $duration = $now - $timestamp;
        if ( $duration > $timeoutSeconds )
            {
                return true;
            }
        return false;
    }

    function create()
    {
        if ( function_exists( 'openssl_random_pseudo_bytes' ) )
            {
                return strtolower( bin2hex( openssl_random_pseudo_bytes( 16 ) ) );
            }
        return strtolower( sprintf( '%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand( 0, 65535 ) ) );
    }

    function save( $otp, $callbackUrl, $successUrl )
    {
        $this->oauth2config->saveOtp( $otp );
        $this->oauth2config->saveOtpSuccessUrl( $successUrl );
        $this->oauth2config->saveOtpCallbackUrl( $callbackUrl );
        $now = microtime( true );
        $this->oauth2config->saveOtpTimestamp( $now );
    }

    function invalidate()
    {
        $this->oauth2config->saveOtp( null );
        $this->oauth2config->saveOtpTimestamp( null );
    }

}
