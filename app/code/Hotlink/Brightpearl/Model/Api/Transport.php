<?php
namespace Hotlink\Brightpearl\Model\Api;

class Transport extends \Hotlink\Brightpearl\Model\Api\Transport\AbstractTransport
{

    protected $curlAdapaterFactory;
    protected $config;

    public function __construct(
        \Hotlink\Framework\Model\ReportFactory $interactionReportFactory,
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper,
        \Hotlink\Brightpearl\Helper\Exception $brightpearlExceptionHelper,
        \Hotlink\Brightpearl\Model\Api\Transport\Client\Adapter\CurlFactory $curlAdapaterFactory,
        \Hotlink\Brightpearl\Model\Config\Authorisation $config
    )
    {
        $this->curlAdapaterFactory = $curlAdapaterFactory;
        $this->config = $config;

        parent::__construct(
            $interactionReportFactory,
            $interactionExceptionHelper,
            $brightpearlExceptionHelper );
    }

    public function _submit(\Hotlink\Framework\Model\Api\Request $request)
    {
        try
            {
                $this
                    ->_setLastRequest($request)
                    ->validate();

                $client = $this->_getClient();
                $client
                    ->setHeaders( $this->getHeaders( $request ) )
                    ->setUri( \Zend\Uri\UriFactory::factory( $this->getBaseUrl() . $request->getAction() ) );

                $sslPeer = $this->config->getCurlValidateCertificate();
                $sslHost = ( $this->config->getCurlValidateHost() ) ? 2 : 0;  // value 1 no longer supported

                $adapter = $client->getAdapter();
                $adapter
                    ->setCurlOption( CURLOPT_CONNECTTIMEOUT_MS, $this->getConnectionTimeout() )
                    ->setCurlOption( CURLOPT_TIMEOUT_MS, $this->getTimeout() )
                    ->setCurlOption( CURLOPT_SSL_VERIFYPEER, $sslPeer )
                    ->setCurlOption( CURLOPT_SSL_VERIFYHOST, $sslHost )
                    ->setCurlOption( CURLINFO_HEADER_OUT, true );

                $method = $request->getMethod();
                $client->setMethod($method);
                $client->setRawBody($request->getBody());

                // execute request
                $response = $client->send();

                if ( !$response->isSuccess() )
                    {
                        throw new \Exception(
                            'Failed API request to Brightpearl: ['.$response->getStatusCode().'] '.$response->getBody(),
                            $response->getStatusCode()
                        );
                    }
                return $response;
            }
        catch ( \Exception $e )
            {
                throw new \Hotlink\Framework\Model\Exception\Transport($e->getMessage(), $e->getCode());
            }
    }

    protected function _getClient()
    {
        if ( is_null( $this->_client ) )
            {
                $client = new \Zend\Http\Client();
                // Zend contains a bug
                //   https://github.com/zendframework/zend-http/issues/19
                //   https://github.com/zendframework/zend-http/pull/53
                //$adapter = new \Zend_Http_Client_Adapter_Curl();
                $adapter = $this->curlAdapaterFactory->create();
                $client->setAdapter($adapter);
                $this->_client = $client;
            }
        return $this->_client;
    }

    protected function _setResult(\Hotlink\Framework\Model\Api\Request $request, $result)
    {
        try
            {
                // Zend can throw exceptions on getBody. i.e. "Error parsing body - doesn't seem to be a chunked message"
                $request->getTransaction()->getResponse()->setContent( $result->getBody() );
                $request->getTransaction()->getResponse()->setHeaders( $result->getHeaders() );
            }
        catch ( \Exception $e )
            {
                throw new \Hotlink\Framework\Model\Exception\Transport( $e->getMessage(), $e->getCode() );
            }
        return $this;
    }

    protected function _setLastRequestInfo()
    {
        $client = $this->_getClient();

        $adapter = $client->getAdapter();

        $rawRequest = $client->getLastRawRequest();
        $rawResponse = $client->getLastRawResponse();

        $this->_setLastRequest( $rawRequest ); // includes headers also
        $this->_setLastResponse( $rawResponse ); // includes headers also
    }

    protected function getHeaders(\Hotlink\Framework\Model\Api\Request $request)
    {
        $headers = array();
        if ( $request->getContentEncoding() == \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest::ENCODING_JSON )
            {
                $headers[] = 'Content-Type: application/json';
            }
        if ( $request->getContentEncoding() == \Hotlink\Brightpearl\Model\Api\Message\Request\AbstractRequest::ENCODING_URLENCODED )
            {
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            }
        return $headers;
    }

    public function setLastFault( $fault )
    {
        return $this->_setLastFault( $fault );
    }
}