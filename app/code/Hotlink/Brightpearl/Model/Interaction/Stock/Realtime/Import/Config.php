<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import;

class Config extends \Hotlink\Brightpearl\Model\Interaction\Stock\Config\AbstractConfig
{
    /**
     * @var \Hotlink\Brightpearl\Helper\Data
     */
    protected $brightpearlHelper;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Hotlink\Framework\Helper\Convention\Interaction\Config $configConvention,
        \Hotlink\Framework\Helper\Config\Field $configFieldHelper,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        \Hotlink\Brightpearl\Model\Config\Shared\General $generalSharedConfig,
        \Hotlink\Brightpearl\Model\Config\Shared\Stock $sharedStockConfig,

        \Hotlink\Brightpearl\Helper\Data $brightpearlHelper,
        array $data = []
    ) {
        $this->brightpearlHelper = $brightpearlHelper;

        parent::__construct(
            $storeManager,
            $scopeConfig,
            $configConvention,
            $configFieldHelper,
            $interaction,
            $generalSharedConfig,
            $sharedStockConfig,
            $data
            );
    }

    public function getDefaultTtl($storeId = null)
    {
        return $this->getConfigData('stock_ttl', $storeId, []);
    }

    public function getStockTtlTriggerMap($storeId = null)
    {
        $value = $this->getConfigData('stock_ttl_trigger_map', $storeId, []);
        return ($value ? $this->brightpearlHelper->getUnserializedOptions( $value ) : []);
    }

    public function getStockTtl( $context, $storeId = null )
    {
        $map = $this->getStockTtlTriggerMap( $storeId );
        foreach ( $map as $item )
            {
                if ( array_key_exists( 'trigger', $item ) )
                    {
                        if ( $item[ 'trigger' ] == $context )
                            {
                                return $item[ 'ttl' ];
                            }
                    }
            }
        return $this->getDefaultTtl( $storeId );
    }

}
