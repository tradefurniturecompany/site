<?php
namespace Hotlink\Brightpearl\Model\Config\Shared;

class Price extends \Hotlink\Brightpearl\Model\Config\AbstractConfig
{

    protected function _getGroup()
    {
        return 'shared_price';
    }

    public function getProductType($storeId = null)
    {
        $value = $this->getConfigData( 'product_type', $storeId, null);
        return is_string($value) ? explode(",", $value) : null;
    }

    public function getBasePriceList($storeId = null)
    {
        return $this->getConfigData( 'base_price_list', $storeId, null);
    }

    public function getPriceAttributeMapping($storeId = null)
    {
        $value = $this->getConfigData( 'attribute_map', $storeId, null);
        return is_string($value) ? $this->_unserialize( $value ) : [];
    }

    public function getCustomerGroupPriceListMap($storeId = null)
    {
        $value = $this->getConfigData( 'customer_group_price_list_map', $storeId, null);
        return is_string($value) ? $this->_unserialize( $value ) : [];
    }

    public function getTierPriceListMap($storeId = null)
    {
        $value = $this->getConfigData( 'tier_price_list_map', $storeId, null);
        return is_string($value) ? $this->_unserialize( $value ) : [];
    }

}