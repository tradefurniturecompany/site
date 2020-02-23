<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Configuration\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{

    function getResponse()
    {
        return $this->_get( 'response' );
    }

    function getConfiguration()
    {
        if ( $reponse = $this->getResponse() )
            {
                if ( isset( $reponse[ 'configuration' ] ) )
                    {
                        return $reponse[ 'configuration' ];
                    }
            }
        return null;
    }

    function getShippingNominalCode()
    {
        if ( $configuration = $this->getConfiguration() )
            {
                if ( isset( $configuration[ 'shippingNominalCode' ] ) )
                    {
                        return $configuration[ 'shippingNominalCode' ];
                    }
            }
        return null;
    }

}