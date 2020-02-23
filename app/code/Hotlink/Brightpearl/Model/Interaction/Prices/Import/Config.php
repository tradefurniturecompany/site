<?php
namespace Hotlink\Brightpearl\Model\Interaction\Prices\Import;

class Config extends \Hotlink\Brightpearl\Model\Interaction\Config\AbstractConfig
{

    function getBatch( $storeId = null )
    {
        return $this->getConfigData( 'batch', $storeId, 100 );
    }

    function getSleep( $storeId = null )
    {
        return $this->getConfigData( 'sleep', $storeId, 5000 );
    }

    function getCheckTaxCompatibility( $storeId = null )
    {
        return $this->getConfigData( 'check_tax_compatibility', $storeId, false );
    }

}