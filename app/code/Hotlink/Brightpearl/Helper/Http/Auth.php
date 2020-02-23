<?php
namespace Hotlink\Brightpearl\Helper\Http;

class Auth
{

    protected $config;

    function __construct( \Hotlink\Brightpearl\Model\Config\Authorisation $config )
    {
        $this->config = $config;
    }

    function getUrl( $url, $storeId )
    {
        if ( $this->config->getHttpAuthEnabled( $storeId ) )
            {
                $username = $this->config->getHttpAuthUsername( $storeId );
                $password = $this->config->getHttpAuthPassword( $storeId );
                $parts = parse_url( $url );
                if ( array_key_exists( 'scheme', $parts )
                     && !array_key_exists( 'user', $parts )
                     && !array_key_exists( 'pass', $parts ) )
                    {
                        $scheme = $parts[ 'scheme' ];
                        $find = $scheme . '://';
                        $replace = $scheme . '://' . $username . ':' . $password . '@';
                        $url = substr_replace( $url, $replace, 0, strlen( $find ) );
                    }
            }
        return $url;
    }

}
