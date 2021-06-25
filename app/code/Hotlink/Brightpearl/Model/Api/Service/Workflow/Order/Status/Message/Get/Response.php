<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Status\Message\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    protected function _validate()
    {
        $content = is_array($this->_content)
            ? $this->_content
            : array();

        $response = array_key_exists( 'response', $content );
        $error    = array_key_exists( 'error', $content );

        if ( ! ($response || $error) ) {
            $this->getExceptionHelper()->throwValidation('Missing value for required response parameter [response | error]');
        }

        return $this;
    }

    function getResponse()
    {
        return $this->_get('response');
    }

    function getError()
    {
        return $this->_content;
    }
}
