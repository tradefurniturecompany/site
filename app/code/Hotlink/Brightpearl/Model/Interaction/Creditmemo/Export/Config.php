<?php
namespace Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export;

class Config extends \Hotlink\Brightpearl\Model\Interaction\Config\AbstractConfig
{

    const KEY_SALES_CREDIT_ORDER_STATUS     = 'sales_credit_order_status';
    const KEY_REFUNDS_ENABLED               = 'refunds_enabled';
    const KEY_REFUNDS_SHIPPING_NOMINAL_CODE = 'refunds_shipping_nominal_code';
    const KEY_QUARANTINE_ENABLED            = 'quarantine_enabled';
    const KEY_QUARANTINE_WAREHOUSE          = 'quarantine_warehouse';
    const KEY_QUARANTINE_WAREHOUSE_LOCATION = 'quarantine_warehouse_location';
    const KEY_QUARANTINE_PRICELIST          = 'quarantine_pricelist';

    protected $configHelper;
    protected $sharedOrderConfig;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Hotlink\Framework\Helper\Convention\Interaction\Config $configConvention,
        \Hotlink\Framework\Helper\Config\Field $configFieldHelper,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,

        \Hotlink\Brightpearl\Helper\Config $configHelper,
        \Hotlink\Brightpearl\Model\Config\Shared\General $generalSharedConfig,
        \Hotlink\Brightpearl\Model\Config\Shared\Order $sharedOrderConfig,

        array $data = []
    )
    {
        $this->configHelper = $configHelper;
        $this->sharedOrderConfig = $sharedOrderConfig;
        parent::__construct( $storeManager,
                             $scopeConfig,
                             $configConvention,
                             $configFieldHelper,
                             $interaction,
                             $generalSharedConfig,
                             $data );
    }

    public function getSalesCreditOrderStatus( $storeId=null )
    {
        return $this->getConfigData( self::KEY_SALES_CREDIT_ORDER_STATUS, $storeId, null );
    }

    public function getRefundsEnabled( $storeId=null )
    {
        return ( boolean ) $this->getConfigData( self::KEY_REFUNDS_ENABLED, $storeId, false );
    }

    public function getRefundsShippingNominalCode( $storeId=null )
    {
        return $this->getConfigData( self::KEY_REFUNDS_SHIPPING_NOMINAL_CODE, $storeId, false );
    }

    public function saveRefundsShippingNominalCode( $value, $storeId = null )
    {
        $section = $this->getSection();
        $group = $this->getGroup();
        return $this->configHelper->saveValue( $section, $group, $value, self::KEY_REFUNDS_SHIPPING_NOMINAL_CODE, $storeId );
    }

    public function getQuarantineEnabled( $storeId=null )
    {
        return ( boolean ) $this->getConfigData( self::KEY_QUARANTINE_ENABLED, $storeId, false );
    }

    public function getQuarantineWarehouse( $storeId=null )
    {
        return $this->getConfigData( self::KEY_QUARANTINE_WAREHOUSE, $storeId, null );
    }

    public function getQuarantineWarehouseLocation( $storeId=null )
    {
        return $this->getConfigData( self::KEY_QUARANTINE_WAREHOUSE_LOCATION, $storeId, null );
    }

    public function getQuarantinePricelist( $storeId=null )
    {
        return $this->getConfigData( self::KEY_QUARANTINE_PRICELIST, $storeId, null );
    }

    public function getUseCurrency( $storeId=null )
    {
        return ( $val = $this->sharedOrderConfig->getUseCurrency( $storeId ) )
            ? $val
            : \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::BASE;
    }

}
