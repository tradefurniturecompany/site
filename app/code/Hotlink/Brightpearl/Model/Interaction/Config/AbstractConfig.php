<?php
namespace Hotlink\Brightpearl\Model\Interaction\Config;

abstract class AbstractConfig extends \Hotlink\Framework\Model\Interaction\Config\AbstractConfig
{
    protected $generalSharedConfig;

    function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Hotlink\Framework\Helper\Convention\Interaction\Config $configConvention,
        \Hotlink\Framework\Helper\Config\Field $configFieldHelper,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,

        \Hotlink\Brightpearl\Model\Config\Shared\General $generalSharedConfig,
        array $data = []
    )
    {
        $this->generalSharedConfig = $generalSharedConfig;

        parent::__construct(
            $storeManager,
            $scopeConfig,
            $configConvention,
            $configFieldHelper,
            $interaction,
            $data );
    }

    function getApiTimeout($storeId = null)
    {
        return $this->getConfigData('api_timeout', $storeId);
    }

    function getChannel( $storeId = null )
    {
        return $this->generalSharedConfig->getChannel($storeId);
    }

}