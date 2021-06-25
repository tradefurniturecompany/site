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

    public function getName()
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

    public function setSubscribeTo($subscribeTo)
    {
        $this->subscribeTo = $subscribeTo;
        return $this;
    }

    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
        return $this;
    }
    public function setUriTemplate($uriTemplate)
    {
        $this->uriTemplate = $uriTemplate;
        return $this;
    }

    public function setBodyTemplate($bodyTemplate)
    {
        $this->bodyTemplate = $bodyTemplate;
        return $this;
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function setIdSetAccepted($idSetAccepted)
    {
        $this->idSetAccepted = $idSetAccepted;
        return $this;
    }

    public function getSubscribeTo()
    {
        return $this->subscribeTo;
    }

    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    public function getUriTemplate()
    {
        return $this->uriTemplate;
    }

    public function getBodyTemplate()
    {
        return $this->bodyTemplate;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function getIdSetAccepted()
    {
        return $this->idSetAccepted;
    }
}