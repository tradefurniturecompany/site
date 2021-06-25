<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Export;

class Config extends \Hotlink\Brightpearl\Model\Interaction\Config\AbstractConfig
{

    const KEY_UPDATE_EXISTING_CUSTOMERS  = 'update_existing_customers';
    const KEY_COMPANY_FROM               = 'company_from';
    const KEY_ALLOCATION_WAREHOUSE       = 'default_allocation_warehouse';
    const KEY_SHIPPING_MAP_ISOMORPHIC    = 'shipping_map_isomorphic';
    const KEY_SHIPPING_MAP_NONISOMORPHIC = 'shipping_map_nonisomorphic';
    const KEY_SHIPPING_DEFAULT           = 'shipping_default';
    const KEY_GIFTMSG_FIELD              = 'giftmessage_field';
    const KEY_PAYMENT_INCLUDE            = 'payment_include';
    const KEY_INCLUDE_MARKETING          = 'include_marketing_details';
    const KEY_CUSTOMISATION_MAP          = 'customisation_map';

    protected $sharedOrderConfig;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Hotlink\Framework\Helper\Convention\Interaction\Config $configConvention,
        \Hotlink\Framework\Helper\Config\Field $configFieldHelper,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        \Hotlink\Brightpearl\Model\Config\Shared\General $generalSharedConfig,

        \Hotlink\Brightpearl\Model\Config\Shared\Order $sharedOrderConfig,

        array $data = []
    )
    {
        $this->sharedOrderConfig = $sharedOrderConfig;
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

    public function getUpdateExistingCustomers( $storeId=null )
    {
        return (boolean) $this->getConfigData( self::KEY_UPDATE_EXISTING_CUSTOMERS, $storeId, true );
    }

    public function getIncludeMarketing( $storeId=null )
    {
        return (boolean) $this->getConfigData( self::KEY_INCLUDE_MARKETING, $storeId, false );
    }

    public function getDefaultAllocationWarehouse( $storeId=null )
    {
        return $this->getConfigData( self::KEY_ALLOCATION_WAREHOUSE, $storeId, false );
    }

    public function getUseCurrency( $storeId=null )
    {
        return ( $val = $this->_getSharedOrderConfig()->getUseCurrency( $storeId ) )
            ? $val
            : \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::BASE;
    }

    public function getShippingMethodDefault( $storeId=null )
    {
        return $this->getConfigData( self::KEY_SHIPPING_DEFAULT, $storeId, null );
    }

    public function getGiftMessageField( $storeId=null )
    {
        return $this->getConfigData( self::KEY_GIFTMSG_FIELD, $storeId, null );
    }

    public function getCompanyFromMap( $storeId = null )
    {
        return $this->getSerializedField( self::KEY_COMPANY_FROM, $storeId, null );
    }

    public function getShippingMethodMapIsomorphic( $storeId=null )
    {
        return $this->getSerializedField( self::KEY_SHIPPING_MAP_ISOMORPHIC, $storeId, null );
    }

    public function getShippingMethodMapNonisomorphic( $storeId=null )
    {
        return $this->getSerializedField( self::KEY_SHIPPING_MAP_NONISOMORPHIC, $storeId, null );
    }

    public function getPaymentMethodMap( $storeId=null )
    {
        return ( $ret = $this->_getSharedOrderConfig()->getPaymentMethodMap( $storeId ) )
            ? $ret
            : [];
    }

    public function getPaymentMethodDefault( $storeId=null )
    {
        return ( $ret = $this->_getSharedOrderConfig()->getPaymentMethodDefault( $storeId ) )
            ? $ret
            : null;
    }

    public function getPaymentMethodDefaultCreateReceipts( $storeId = null )
    {
        return ( $ret = $this->_getSharedOrderConfig()->getPaymentMethodDefaultCreateReceipts( $storeId ) )
            ? $ret
            : null;
    }

    public function getOrderStatusMap( $storeId=null )
    {
        return ( $ret = $this->_getSharedOrderConfig()->getOrderStatusMap( $storeId ) )
            ? $ret
            : [];
    }

    public function getOrderStatusDefault( $storeId=null )
    {
        return ( $ret = $this->_getSharedOrderConfig()->getOrderStatusDefault( $storeId ) )
            ? $ret
            : null;
    }

    public function getCustomisationMap( $storeId = null )
    {
        $customisations = $this->getSerializedField( self::KEY_CUSTOMISATION_MAP, $storeId, null );
        if ( $customisations )
            {
                unset( $customisations[ '__empty' ] );
            }
        return $customisations;
    }

    protected function _getSharedOrderConfig()
    {
        return $this->sharedOrderConfig;
    }

}