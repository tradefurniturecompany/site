<?php
namespace Hotlink\Framework\Model\Interaction\Config;

abstract class AbstractConfig extends \Hotlink\Framework\Model\Config\AbstractConfig
{

    const FIELD_EVENTS = 'events';        // The field suffix given in system.xml must match this.

    protected $configConvention;
    protected $interaction;

    function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Hotlink\Framework\Helper\Convention\Interaction\Config $configConvention,
        \Hotlink\Framework\Helper\Config\Field $configFieldHelper,

        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        array $data = []
    )
    {
        $this->configConvention = $configConvention;
        $this->interaction = $interaction;
        parent::__construct( $storeManager,
                             $scopeConfig,
                             $configFieldHelper,
                             $data );
    }

    protected function _getSection()
    {
        return $this->configConvention->getSectionKey( $this->interaction );
    }

    protected function _getGroup()
    {
        return $this->configConvention->getGroupKey( $this->interaction );
    }

    function isEnabled( $storeId = null )
    {
        return $this->getConfigData( 'enabled', $storeId, false );
    }

    function isTriggerEnabled( \Hotlink\Framework\Model\Trigger\AbstractTrigger $trigger, $storeId = null )
    {
        $context = $trigger->getContext();
        if ( !$context ) return false;
        return $this->getConfigData( $context, $storeId, false );
    }

    function getImplementationModel( $storeId )
    {
        return $this->getConfigData( 'interaction_implementation', $storeId, false );
    }

}
