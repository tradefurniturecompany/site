<?php
namespace Magesales\Shippingtable\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_countryCollection;
    protected $_regionCollection;
	protected $_customergroupCollection;
	protected $_eavConfig;
	
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customergroupCollection,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollection,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollection,
		\Magento\Eav\Model\Config $eavConfig
    ) {
        parent::__construct($context);
		$this->_customergroupCollection = $customergroupCollection;
        $this->_countryCollection = $countryCollection;
        $this->_regionCollection = $regionCollection;
		$this->_eavConfig = $eavConfig;
    }
	
    public function getAllGroups()
    {
        $customerGroups = $this->_customergroupCollection->create()->load()->toOptionArray();

        $found = false;
        foreach ($customerGroups as $group) {
            if ($group['value']==0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, ['value'=>0, 'label'=>__('NOT LOGGED IN')]);
        } 
        
        return $customerGroups;
    }
    
    public function getStatuses()
    {
        return [
            '0' => __('Inactive'),
            '1' => __('Active'),
        ];       
    }
      
    public function getStates()
    {
        $hash = [];
        $hashCountry = $this->getCountries();
        
        $collection = $this->_regionCollection->create()->getData();

        foreach ($collection as $state)
		{
            $hash[$state['region_id']] = $hashCountry[$state['country_id']] ."/".$state['default_name'];
        }
        asort($hash);
        $hashAll['0'] = 'All';
        $hash = $hashAll + $hash;        
        return $hash;    
    }
        
    public function getCountries()
    {
        $hash = [];
        $countries = $this->_countryCollection->create()->toOptionArray();

        foreach ($countries as $country){
            if($country['value']){
                $hash[$country['value']] = $country['label'];                
            }
        }
        asort($hash);
        $hashAll['0'] = 'All';
        $hash = $hashAll + $hash; 
        return $hash;    
    } 
   
    public function getTypes()
    {
        $hash = [];
        $attribute = $this->_eavConfig->getAttribute('catalog_product', 'shipping_type');
        if ($attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);
        }
        foreach ($options as $option){
            $hash[$option['value']] = $option['label'];    
        }
        asort($hash);
        $hashAll['0'] = 'All';
        $hash = $hashAll + $hash; 
        return $hash;
    }    
}
