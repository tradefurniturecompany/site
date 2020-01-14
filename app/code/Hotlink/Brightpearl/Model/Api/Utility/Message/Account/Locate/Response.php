<?php
namespace Hotlink\Brightpearl\Model\Api\Utility\Message\Account\Locate;

class Response extends \Hotlink\Brightpearl\Model\Api\Message\Response\AbstractResponse
{

    protected function _validate()
    {
        $authorizeInstance = $this->getAuthorizeInstanceUrl();
        if (!$authorizeInstance)
            $this->getExceptionHelper()->throwValidation('Missing value for required response parameter [response.urls.authorizeInstance]');
        return $this;
    }

    public function getApiDomain()
    {
        return $this->_get(array('response', 'apiDomain'));
    }

    public function getDatacentreCode()
    {
        return $this->_get(array('response', 'datacentreCode'));
    }

    public function getLoginUrl()
    {
        return $this->_get(array('response', 'urls', 'login'));
    }

    public function getAuthorizeServerUrl()
    {
        return $this->_get(array('response', 'urls', 'authorizeServer'));
    }

    public function getAuthorizeInstanceUrl()
    {
        return $this->_get(array('response', 'urls', 'authorizeInstance'));
    }
}