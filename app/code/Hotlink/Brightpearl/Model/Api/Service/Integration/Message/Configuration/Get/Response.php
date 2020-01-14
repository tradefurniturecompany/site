<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Configuration\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{

    public function getResponse()
    {
        return $this->_get( 'response' );
    }

    public function getConfiguration()
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

    public function getShippingNominalCode()
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