<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Message\Response;

abstract class AbstractResponse extends \Hotlink\Brightpearl\Model\Api\Message\Response\AbstractResponse
{
    protected function _validate()
    {
        if ( !is_array( $this->_content ) || !array_key_exists( 'response', $this->_content ) ) {
            $this->getExceptionHelper()->throwValidation( 'Missing value for required response parameter [response]' );
        }

        return $this;
    }
}