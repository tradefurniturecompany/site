<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Config;

abstract class AbstractConfig extends \Hotlink\Brightpearl\Model\Interaction\Config\AbstractConfig
{

    protected $sharedStockConfig;

    function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Hotlink\Framework\Helper\Convention\Interaction\Config $configConvention,
        \Hotlink\Framework\Helper\Config\Field $configFieldHelper,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        \Hotlink\Brightpearl\Model\Config\Shared\General $generalSharedConfig,

        \Hotlink\Brightpearl\Model\Config\Shared\Stock $sharedStockConfig,
        array $data = []
    )
    {
        $this->sharedStockConfig = $sharedStockConfig;
        parent::__construct(
            $storeManager,
            $scopeConfig,
            $configConvention,
            $configFieldHelper,
            $interaction,
            $generalSharedConfig,
            $data
        );
    }

    function getWarehouses($storeId = null)
    {
        return $this->_getSharedStockConfig()->getWarehouses($storeId);
    }

    function getBatch($storeId = null)
    {
        return $this->getConfigData('batch', $storeId, 50);
    }

    function getSleep($storeId = null)
    {
        return $this->getConfigData('sleep', $storeId, 5000);
    }

    function getPutBackInstock($storeId = null)
    {
        return $this->_getSharedStockConfig()->getPutBackInstockFlag($storeId);
    }

    function getQtyZeroWhenMissing($storeId = null)
    {
        return $this->_getSharedStockConfig()->getQtyZeroWhenMissingFlag($storeId);
    }

    function getSkipUnmanaged($storeId = null)
    {
        return $this->_getSharedStockConfig()->getStockSkipUnmanaged($storeId);
    }

    protected function _getSharedStockConfig()
    {
        return $this->sharedStockConfig;
    }

}
