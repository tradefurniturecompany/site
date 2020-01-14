<?php
namespace Hotlink\Brightpearl\Model\Api\Authorisation\Message\Authorise;

class Response extends \Hotlink\Brightpearl\Model\Api\Message\Response\AbstractResponse
{

    protected function _validate()
    {
        if (!$this->getInstanceToken()) {
            $this->getExceptionHelper()->throwValidation( 'Missing value for required response parameter [response]' );
        }
        return $this;
    }

    public function getInstanceToken()
    {
        return $this->_get( 'response' );
    }
}