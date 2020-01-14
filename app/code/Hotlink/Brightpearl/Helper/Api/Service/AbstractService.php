<?php
namespace Hotlink\Brightpearl\Helper\Api\Service;

abstract class AbstractService extends \Hotlink\Brightpearl\Helper\Api\AbstractApi
{

    protected $brightpearlConfigAuthorisation;
    protected $brightpearlConfigOAuth2;
    protected $brightpearlApiServiceTransportFactory;

    public function __construct(
        \Hotlink\Brightpearl\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Brightpearl\Model\Config\Api $brightpearlConfigApi,

        \Hotlink\Brightpearl\Model\Config\Authorisation $brightpearlConfigAuthorisation,
        \Hotlink\Brightpearl\Model\Config\OAuth2 $brightpearlConfigOAuth2,
        \Hotlink\Brightpearl\Model\Api\Service\TransportFactory $brightpearlApiServiceTransportFactory
    )
    {
        parent::__construct( $exceptionHelper, $reportHelper, $brightpearlConfigApi );
        $this->brightpearlConfigAuthorisation = $brightpearlConfigAuthorisation;
        $this->brightpearlConfigOAuth2 = $brightpearlConfigOAuth2;
        $this->brightpearlApiServiceTransportFactory = $brightpearlApiServiceTransportFactory;
    }

    protected function _getTransport($storeId = null, $timeout = null)
    {
        $oauth2    = $this->brightpearlConfigOAuth2;
        $config    = $this->brightpearlConfigAuthorisation;
        $transport = $this->brightpearlApiServiceTransportFactory->create();

        if ( $token = $oauth2->getAccessToken( $storeId ) )
            {
                $transport
                    ->setBaseUrl( $oauth2->getApiDomain( $storeId ) )
                    ->setToken( $config->getToken( $storeId ) )
                    ->setOAuth2Token( $oauth2->getAccessToken( $storeId ) );
            }
        else
            {
                $transport
                    ->setBaseUrl( $config->getApiDomain( $storeId ) )
                    ->setToken( $config->getToken( $storeId ) )
                    ->setOAuth2Token( $oauth2->getAccessToken( $storeId ) );
            }
        if ( !is_null( $timeout ) )
            {
                $transport->setTimeout( $timeout );
            }
        return $transport;
    }

}