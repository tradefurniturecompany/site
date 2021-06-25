<?php
namespace Hotlink\Brightpearl\Helper\Api;

class OAuth2 extends \Hotlink\Brightpearl\Helper\Api\AbstractApi
{

    const OAUTH2_PROXY_DOMAIN = "https://magento.brightpearlconnect.com";

    protected $apiAuthoriseFactory;
    protected $apiRefreshFactory;
    protected $apiTransportFactory;
    protected $oauth2config;

    public function __construct(
        \Hotlink\Brightpearl\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Brightpearl\Model\Config\Api $brightpearlConfigApi,

        \Hotlink\Brightpearl\Model\Api\OAuth2\Transaction\AuthoriseFactory $apiAuthoriseFactory,
        \Hotlink\Brightpearl\Model\Api\OAuth2\Transaction\RefreshFactory $apiRefreshFactory,
        \Hotlink\Brightpearl\Model\Api\OAuth2\TransportFactory $apiTransportFactory,
        \Hotlink\Brightpearl\Model\Config\OAuth2 $oauth2config
    )
    {
        parent::__construct( $exceptionHelper, $reportHelper, $brightpearlConfigApi );
        $this->apiAuthoriseFactory = $apiAuthoriseFactory;
        $this->apiRefreshFactory = $apiRefreshFactory;
        $this->apiTransportFactory = $apiTransportFactory;
        $this->oauth2config = $oauth2config;
    }

    public function getName()
    {
        return 'OAuth2 API';
    }

    public function requestAccessToken( $storeId, $account, $code, $redirectUri, $clientId, $timeout = 5000 )
    {
        $this
            ->_assertNotEmpty( 'storeId', $storeId )
            ->_assertNotEmpty( 'account', $account )
            ->_assertNotEmpty( 'code', $code );

        $transaction = $this->apiAuthoriseFactory->create()
            ->setAccount( $account )
            ->setCode( $code )
            ->setRedirectUri( $redirectUri )
            ->setClientId( $clientId );

        $transport = $this->_getTransport( $storeId, $timeout );
        $transport->setBaseUrl( self::OAUTH2_PROXY_DOMAIN );

        $response = $this->submit( $transaction, $transport );

        return $response;
    }

    public function refreshAccessToken( $storeId, $account, $refreshToken, $timeout = 5000 )
    {
        $this
            ->_assertNotEmpty( 'account', $account )
            ->_assertNotEmpty( 'refreshToken', $refreshToken );

        $transaction = $this->apiRefreshFactory->create()
                     ->setAccount( $account )
                     ->setClientId( \Hotlink\Brightpearl\Model\Platform::APP_REF_M2 )
                     ->setRefreshToken( $refreshToken );

        $transport = $this->_getTransport( $storeId, $timeout );
        $transport->setBaseUrl( $this->oauth2config->getApiDomain( $storeId ) );

        $response = $this->submit( $transaction, $transport );

        return $response;
    }

    protected function _getTransport( $storeId = null, $timeout = null )
    {
        $transport = $this->apiTransportFactory->create();
        if ( !is_null( $timeout ) ) $transport->setTimeout( $timeout );

        return $transport;
    }

}
