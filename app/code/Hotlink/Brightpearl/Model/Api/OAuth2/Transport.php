<?php
namespace Hotlink\Brightpearl\Model\Api\OAuth2;

class Transport extends \Hotlink\Brightpearl\Model\Api\Transport
{

    public function getDevRef()
    {
        return \Hotlink\Brightpearl\Model\Platform::DEV_REF;
    }

    public function getAppRef()
    {
        return \Hotlink\Brightpearl\Model\Platform::APP_REF_M2;
    }

    protected function validate()
    {
        parent::validate();

        if ( !\Zend_Validate::is($this->getDevRef(), 'NotEmpty') ) {
            $this->_exceptionHelper()->throwTransport('Missing required [devRef]');
        }

        if ( !\Zend_Validate::is($this->getAppRef(), 'NotEmpty') ) {
            $this->_exceptionHelper()->throwTransport('Missing required [appRef]');
        }

        return $this;
    }

    public function getHeaders(\Hotlink\Framework\Model\Api\Request $request)
    {
        return array_merge( parent::getHeaders( $request ),
                            array( 'brightpearl-dev-ref: '.$this->getDevRef(),
                                   'brightpearl-app-ref: '.$this->getAppRef() ) );
    }

}