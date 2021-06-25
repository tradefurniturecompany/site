<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Webhook\Post;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Post\AbstractPost
{
    public function getFunction()
    {
        return $this->getMethod(). " integration-service/webhook";
    }

    public function getAction()
    {
        return sprintf('/public-api/%s/integration-service/webhook',
                       $this->getTransaction()->getAccountCode());
    }

    public function getBody()
    {
        $transaction = $this->getTransaction();
        return $this->_encodeJson( [ 'subscribeTo'   => $transaction->getSubscribeTo(),
                                     'httpMethod'    => $transaction->getHttpMethod(),
                                     'uriTemplate'   => $transaction->getUriTemplate(),
                                     'bodyTemplate'  => $transaction->getBodyTemplate(),
                                     'contentType'   => $transaction->getContentType(),
                                     'idSetAccepted' => $transaction->getIdSetAccepted() ] );
    }

    public function validate()
    {
        try {
            \Zend_Uri::factory($this->getTransaction()->getUriTemplate());
        }
        catch( \Zend_Uri_Exception $e ) {
            $this->getExceptionHelper()->throwValidation( 'Invalid UriTemplate: '.$e->getMessage() );
        }

        $validHttpMethods = [
            \Zend_Http_Client::GET,
            \Zend_Http_Client::POST,
            \Zend_Http_Client::PUT,
            \Zend_Http_Client::HEAD,
            \Zend_Http_Client::DELETE,
            \Zend_Http_Client::TRACE,
            \Zend_Http_Client::OPTIONS,
            \Zend_Http_Client::CONNECT,
            \Zend_Http_Client::MERGE,
            'PATCH'
            ];

        if ( !in_array( strtoupper( $this->getTransaction()->getHttpMethod()), $validHttpMethods) )
            $this->getExceptionHelper()->throwValidation( $this->getTransaction()->getHttpMethod().' is not a valid HTTP request method.' );
    }
}