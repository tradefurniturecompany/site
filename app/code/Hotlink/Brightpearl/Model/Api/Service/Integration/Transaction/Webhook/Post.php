<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Transaction\Webhook;

class Post extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{
    protected $subscribeTo;
	protected $httpMethod;
	protected $uriTemplate;
	protected $bodyTemplate;
    protected $contentType;
    protected $idSetAccepted;

    function getName()
    {
        return 'Create webhook';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Webhook\Post\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Webhook\Post\Response';
    }

    function setSubscribeTo($subscribeTo)
    {
        $this->subscribeTo = $subscribeTo;
        return $this;
    }

    function setHttpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
        return $this;
    }
    function setUriTemplate($uriTemplate)
    {
        $this->uriTemplate = $uriTemplate;
        return $this;
    }

    function setBodyTemplate($bodyTemplate)
    {
        $this->bodyTemplate = $bodyTemplate;
        return $this;
    }

    function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    function setIdSetAccepted($idSetAccepted)
    {
        $this->idSetAccepted = $idSetAccepted;
        return $this;
    }

    function getSubscribeTo()
    {
        return $this->subscribeTo;
    }

    function getHttpMethod()
    {
        return $this->httpMethod;
    }

    function getUriTemplate()
    {
        return $this->uriTemplate;
    }

    function getBodyTemplate()
    {
        return $this->bodyTemplate;
    }

    function getContentType()
    {
        return $this->contentType;
    }

    function getIdSetAccepted()
    {
        return $this->idSetAccepted;
    }
}