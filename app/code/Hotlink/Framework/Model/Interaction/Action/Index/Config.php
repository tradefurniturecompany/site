<?php
namespace Hotlink\Framework\Model\Interaction\Action\Index;

class Config extends \Hotlink\Framework\Model\Interaction\Action\Config\AbstractConfig
{
    function getBefore()
    {
        return $this->interaction->getConfig()->getConfigData( 'action_index_before' );
    }

    function getAfter()
    {
        return $this->interaction->getConfig()->getConfigData( 'action_index_after' );
    }

    function getAfterRebuild()
    {
        return $this->interaction->getConfig()->getCsvField( 'action_index_after_rebuild' );
    }
}
