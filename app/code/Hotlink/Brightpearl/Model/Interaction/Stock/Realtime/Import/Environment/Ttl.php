<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment;

class Ttl extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Config\AbstractConfig
{

    protected $_context = false;

    function getDefault()
    {
        return 60 * 60;
    }

    function getName()
    {
        return "Time-to-live";
    }

    function getKey()
    {
        return 'stock_ttl';
    }

    function getNote()
    {
        return 'Time-to-live (TTL) in seconds';
    }

    function getValue()
    {
        if ( !$this->_valueInitialised )
            {
                $ttl = null;
                $environment = $this->getEnvironment();
                $storeId = $environment->getStoreId();
                $config = $environment->getConfig();
                $trigger = null;
                if ( $interaction = $environment->getInteraction() )
                    {
                        $trigger = $interaction->getTrigger();
                    }
                if ( ! is_null( $trigger ) )   // trigger is null during rendering of interaction admin form
                    {
                        $context = $this->getEnvironment()->getInteraction()->getTrigger()->getContext();
                        $ttl = $config->getStockTtl( $context, $storeId );
                    }
                if ( is_null( $ttl ) )
                    {
                        $ttl = $this->getDefault();
                    }
                $this->setValue( $ttl );
            }
        return $this->_value;
    }

}
