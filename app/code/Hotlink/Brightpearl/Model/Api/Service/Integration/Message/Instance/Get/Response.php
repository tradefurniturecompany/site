<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Instance\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{

    public function getInstance()
    {
        return $this->_get( 'response' );
    }

    protected function _getInstanceField( $field )
    {
        if ( $instance = $this->getInstance() )
            {
                return ( isset( $instance[ $field ] ) )
                    ? $instance[ $field ]
                    : null;
            }
        return null;
    }

    public function getInstalledIntegrationInstanceId()
    {
        return $this->_getInstanceField( 'installedIntegrationInstanceId' );
    }

    public function getInstalledIntegrationId()
    {
        return $this->_getInstanceField( 'installedIntegrationId' );
    }

    public function getInstanceName()
    {
        return $this->_getInstanceField( 'instanceName' );
    }

}