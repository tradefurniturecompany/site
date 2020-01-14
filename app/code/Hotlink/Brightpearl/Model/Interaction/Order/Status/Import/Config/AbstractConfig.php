<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Import\Config;

class AbstractConfig extends \Hotlink\Brightpearl\Model\Interaction\Config\AbstractConfig
{

    protected $orderSharedConfig;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Hotlink\Framework\Helper\Convention\Interaction\Config $configConvention,
        \Hotlink\Framework\Helper\Config\Field $configFieldHelper,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        \Hotlink\Brightpearl\Model\Config\Shared\General $generalSharedConfig,

        \Hotlink\Brightpearl\Model\Config\Shared\Order $orderSharedConfig,
        array $data = []
        )
    {
        $this->orderSharedConfig = $orderSharedConfig;

        parent::__construct(
            $storeManager,
            $scopeConfig,
            $configConvention,
            $configFieldHelper,
            $interaction,
            $generalSharedConfig,
            $data );
    }

    public function getOrderStatusMap( $storeId = null )
    {
        return ( $map = $this->orderSharedConfig->getOrderStatusMap( $storeId ) )
            ? $map
            : array();
    }
}
