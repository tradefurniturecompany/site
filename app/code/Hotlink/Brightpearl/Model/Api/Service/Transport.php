<?php
namespace Hotlink\Brightpearl\Model\Api\Service;

class Transport extends \Hotlink\Brightpearl\Model\Api\Transport
{

    protected $_token;
    protected $_oauth2token;

    protected $oauth2helper;
    protected $oauth2config;

    function __construct(
        \Hotlink\Framework\Model\ReportFactory $interactionReportFactory,
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper,
        \Hotlink\Brightpearl\Helper\Exception $brightpearlExceptionHelper,
        \Hotlink\Brightpearl\Model\Api\Transport\Client\Adapter\CurlFactory $curlAdapaterFactory,
        \Hotlink\Brightpearl\Model\Config\Authorisation $config,

        \Hotlink\Brightpearl\Model\Config\OAuth2 $oauth2config,
        \Hotlink\Brightpearl\Helper\Api\OAuth2 $oauth2helper
    )
    {
        $this->oauth2config = $oauth2config;
        $this->oauth2helper = $oauth2helper;
        parent::__construct( $interactionReportFactory,
                             $interactionExceptionHelper,
                             $brightpearlExceptionHelper,
                             $curlAdapaterFactory,
                             $config );
    }

    function submit( \Hotlink\Framework\Model\Api\Request $request )
    {
        try
            {
                return parent::submit( $request );
            }
        catch ( \Hotlink\Framework\Model\Exception\Transport $fault )
            {
                if ( $fault->getCode() == 401 )
                    {
                        if ( $this->oauth2config->isActive() )
                            {
                                $report = $this->getReport();
                                $report->error( "http 401 " . $fault->getMessage() );
                                //
                                //  refresh the token
                                //
                                $storeId = null;
                                $account = $request->getTransaction()->getAccountCode();
                                $refreshTokenOld = $this->oauth2config->getRefreshToken( $storeId );
                                $response = $report( $this->oauth2helper, 'refreshAccessToken', $storeId, $account, $refreshTokenOld );

                                $accessTokenNew = $response->getAccessToken();
                                $tokenTypeNew = $response->getTokenType();
                                $expiresInNew = $response->getExpiresIn();
                                $refreshTokenNew = $response->getRefreshToken();
                                $installationInstanceIdNew = $response->getInstallationInstanceId();
                                $apiDomainNew = $response->getApiDomain();

                                if ( $accessTokenNew && ( $accessTokenNew != $this->oauth2config->getAccessToken() ) )
                                    {
                                        $this->oauth2config->saveAccessToken( $accessTokenNew );
                                    }
                                if ( $expiresInNew )
                                    {
                                        $this->oauth2config->saveExpiresIn( $expiresInNew );
                                    }
                                if ( $refreshTokenNew && ( $refreshTokenNew != $refreshTokenOld ) )
                                    {
                                        $this->oauth2config->saveRefreshToken( $refreshTokenNew );
                                    }
                                /* changing these is high risk! */
                                if ( $installationInstanceIdNew
                                     && ( $installationInstanceIdNew != $this->oauth2config->getInstallationInstanceId() ) )
                                    {
                                        $this->oauth2config->saveInstallationInstanceId( $installationInstanceIdNew );
                                    }
                                if ( $apiDomainNew && ( $apiDomainNew != $this->oauth2config->getApiDomain() ) )
                                    {
                                        $this->oauth2config->saveApiDomain( $apiDomainNew );
                                    }
                                return parent::submit( $request );
                            }
                    }
                else
                    {
                        throw $fault;
                    }
            }
    }

    function setToken( $token )
    {
        $this->_token = $token;
        return $this;
    }

    function getToken()
    {
        return $this->_token;
    }

    function setOAuth2Token( $value )
    {
        $this->_oauth2token = $value;
        return $this;
    }

    function getOAuth2Token()
    {
        return $this->_oauth2token;
    }

    function getDevRef()
    {
        return \Hotlink\Brightpearl\Model\Platform::DEV_REF;
    }

    function getAppRef()
    {
        return \Hotlink\Brightpearl\Model\Platform::APP_REF_M2;
    }

    protected function validate()
    {
        parent::validate();

        if ( !\Zend_Validate::is( $this->getOAuth2Token(), 'NotEmpty' )
             && !\Zend_Validate::is( $this->getToken(), 'NotEmpty' ) )
            {
                $this->_exceptionHelper()->throwTransport( 'Missing both [token] and [oauth2token]' );
            }

        if (!\Zend_Validate::is($this->getDevRef(), 'NotEmpty')) {
            $this->_exceptionHelper()->throwTransport('Missing required [devRef]');
        }

        if (!\Zend_Validate::is($this->getAppRef(), 'NotEmpty')) {
            $this->_exceptionHelper()->throwTransport('Missing required [appRef]');
        }

        return $this;
    }

    protected function getHeaders(\Hotlink\Framework\Model\Api\Request $request)
    {
        $headers = array_merge( parent::getHeaders( $request ),
                                [ 'brightpearl-dev-ref: '.$this->getDevRef(),
                                  'brightpearl-app-ref: '.$this->getAppRef()
                                ] );
        if ( $token = $this->getToken() )
            {
                $headers = array_merge( $headers, [ 'brightpearl-instance-token: ' . $token ] );
            }
        if ( $token = $this->getOAuth2Token() )
            {
                $headers = array_merge( $headers, [ 'Authorization: Bearer ' . $token ] );
            }
        return $headers;
    }

}