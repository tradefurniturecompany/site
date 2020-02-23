<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Instance\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{

    function getInstance()
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

    function getInstalledIntegrationInstanceId()
    {
        return $this->_getInstanceField( 'installedIntegrationInstanceId' );
    }

    function getInstalledIntegrationId()
    {
        return $this->_getInstanceField( 'installedIntegrationId' );
    }

    function getInstanceName()
    {
        return $this->_getInstanceField( 'instanceName' );
    }

}