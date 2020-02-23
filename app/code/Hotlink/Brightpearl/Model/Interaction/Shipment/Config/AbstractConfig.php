<?php
namespace Hotlink\Brightpearl\Model\Interaction\Shipment\Config;

class AbstractConfig extends \Hotlink\Brightpearl\Model\Interaction\Config\AbstractConfig
{

    protected $sharedShipmentConfig;

    function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Hotlink\Framework\Helper\Convention\Interaction\Config $configConvention,
        \Hotlink\Framework\Helper\Config\Field $configFieldHelper,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        \Hotlink\Brightpearl\Model\Config\Shared\General $generalSharedConfig,

        \Hotlink\Brightpearl\Model\Config\Shared\Shipment $sharedShipmentConfig,
        array $data = []
    )
    {
        $this->sharedShipmentConfig = $sharedShipmentConfig;

        parent::__construct(
            $storeManager,
            $scopeConfig,
            $configConvention,
            $configFieldHelper,
            $interaction,
            $generalSharedConfig,
            $data );
    }

    function getNotifyCustomer($storeId = null)
    {
        return $this->sharedShipmentConfig->getNotifyCustomer($storeId);
    }

    function getShippingOptionsMap( $storeId = null)
    {
        return $this->sharedShipmentConfig->getShippingOptionsMap($storeId);
    }
}
