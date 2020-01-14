<?php
namespace Magesales\Shippingtable\Model\ResourceModel\Rate;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function _construct()
    {
        $this->_init('Magesales\Shippingtable\Model\Rate', 'Magesales\Shippingtable\Model\ResourceModel\Rate');
	}
    
    public function addAddressFilters($request)
    {
        $this->addFieldToFilter('country', [
            [
                'like'  => $request->getDestCountryId(),
            ],
            [
                'eq'    => '0',
            ],
            [
                'eq'    => '',
            ],                                                                  
        ]);
        
        $this->addFieldToFilter('state', [
                                [
                                'like'  => $request->getDestRegionId(),
                                 ],
                                [
                                'eq'    => '0',
                                 ],
                                [
                                'eq'    => '',
                                 ],                                                                  
        ]);
        
        $this->addFieldToFilter('city', [
                                [
                                'like'  => $request->getDestCity(),
                                 ],
                                [
                                'eq'    => '',
                                 ],                                                                  
        ]);
        $configValue = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface');
        if ($configValue->getValue('carriers/shippingtable/numeric_zip',\Magento\Store\Model\ScopeInterface::SCOPE_STORE))
        {
            $this->addFieldToFilter('zip_from', [
                                    [
                                    'lteq'  => $request->getDestPostcode(),
                                     ],
                                    [
                                    'eq'    => '',
                                     ],                                                                  
            ]);
            $this->addFieldToFilter('zip_to', [
                                    [
                                    'gteq'  => $request->getDestPostcode(),
                                     ],
                                    [
                                    'eq'    => '',
                                     ],                                                                  
            ]);                          
        }
        else
            $this->getSelect()->where("? LIKE zip_from OR zip_from = ''", $request->getDestPostcode());

        return $this;        
    }    
    
    public function addMethodFilters($methodIds)
    {
        $this->addFieldToFilter('method_id', ['in'=>$methodIds]);  
                                         
        return $this;    
    } 
       
    public function addTotalsFilters($totals,$shippingType)
    {
        $this->addFieldToFilter('price_from', ['lteq'=>$totals['not_free_price']]);
        $this->addFieldToFilter('price_to', ['gteq'=>$totals['not_free_price']]);
        $this->addFieldToFilter('weight_from', ['lteq'=>$totals['not_free_weight']]);
        $this->addFieldToFilter('weight_to', ['gteq'=>$totals['not_free_weight']]);
        $this->addFieldToFilter('qty_from', ['lteq'=>$totals['not_free_qty']]);
        $this->addFieldToFilter('qty_to', ['gteq'=>$totals['not_free_qty']]);
        $this->addFieldToFilter('shipping_type', [
                                    [
                                    'eq'  => $shippingType,
                                     ],
                                    [
                                    'eq'    => '',
                                     ],
                                    [
                                    'eq'    => '0',
                                     ],                                                                                                             
            ]);                         
        return $this;
        
    }
}