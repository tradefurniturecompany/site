<?php
namespace Magesales\Shippingtable\Model;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Rate extends \Magento\Framework\Model\AbstractModel
{
    const MAX_LINE_LENGTH  = 50000;
    const COL_NUMS         = 17;
    const BATCH_SIZE       = 50000;
    const COUNTRY          = 0;
    const STATE            = 1;
    const ZIP_FROM         = 3;
    const PRICE_TO         = 6;
    const WEIGHT_TO        = 8;
    const QTY_TO           = 10;
    const SHIPPING_TYPE    = 11;
    const ALGORITHM_SUM    = 0;
    const ALGORITHM_MAX    = 1;
    const ALGORITHM_MIN    = 2;
	
	protected $_methodFactory;
	protected $_scopeConfig;
	protected $_productFactory;
	protected $_helper;
	protected $_regionCollection;
	protected $_countryCollection;
	protected $_localeLists;
	
	public function __construct(
		Context $context,
		Registry $registry,
		\Magesales\Shippingtable\Model\MethodFactory $methodFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magesales\Shippingtable\Helper\Data $helper,
		\Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollection,
		\Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollection,
		\Magento\Framework\Locale\ListsInterface $localeLists)
	{
		$this->_scopeConfig = $scopeConfig;
		$this->_methodFactory = $methodFactory;
		$this->_productFactory = $productFactory;
		$this->_countryCollection = $countryCollection;
		$this->_regionCollection = $regionCollection;
		$this->_helper = $helper;
		$this->_localeLists = $localeLists;
		
        parent::__construct($context, $registry);
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init('Magesales\Shippingtable\Model\ResourceModel\Rate');
    }
    
    public function findBy($request, $collection)
    {
		if (!$request->getAllItems()) {
            return [];
        }

        if($collection->getSize() == 0)
        {
            return [];
        }
        
        $methodIds = [];        
        foreach ($collection as $method)
        {
           $methodIds[] = $method->getMethodId();
		}
		
        // calculate price and weight
        $allowFreePromo = $this->_scopeConfig->getValue('carriers/shippingtable/allow_promo', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);  
        $ignoreVirtual   = $this->_scopeConfig->getValue('carriers/shippingtable/ignore_virtual', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
          
        $items = $request->getAllItems();
        $shippingTypes = [];
        $shippingTypes[] = 0;
        foreach($items as $item)
        {
            // if attribute isn't load to product
            $product = $this->_productFactory->create()->load($item->getProduct()->getEntityId());
            if ($product->getShippingType()){
                $shippingTypes[] = $product->getShippingType();                
            } else {
               $shippingTypes[] = 0; 
            }
        }
        
        $shippingTypes = array_unique($shippingTypes);
        $allCosts = []; 
        
        $allRates = $this->getResourceCollection();
        $allRates->addMethodFilters($methodIds);
        $ratesTypes = [];
        
        foreach ($allRates as $singleRate){
            $ratesTypes[$singleRate->getMethodId()][]= $singleRate->getShippingType();    
        }
                
        $intersectTypes = [];
        
        foreach ($ratesTypes as $key => $value)
		{
            $intersectTypes[$key] = array_intersect($shippingTypes,$value);
            arsort($intersectTypes[$key]);
            $methodIds = [$key];
            $allTotals =  $this->calculateTotals($request, $ignoreVirtual, $allowFreePromo,'0');
            
            foreach ($intersectTypes[$key] as $shippingType)
			{
                $totals = $this->calculateTotals($request, $ignoreVirtual, $allowFreePromo,$shippingType);
                if ($allTotals['qty'] > 0 && (!$this->_scopeConfig->getValue('carriers/shippingtable/dont_split', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) || $allTotals['qty'] == $totals['qty'])) {

                    if ($shippingType == 0)
                        $totals = $allTotals;
                        
                    $allTotals['not_free_price'] -= $totals['not_free_price'];
                    $allTotals['not_free_weight'] -= $totals['not_free_weight'];
                    $allTotals['not_free_qty'] -= $totals['not_free_qty'];
                    $allTotals['qty'] -= $totals['qty'];
 
                    $allRates = $this->getResourceCollection();
                    $allRates->addAddressFilters($request);
                    $allRates->addTotalsFilters($totals,$shippingType);
                    $allRates->addMethodFilters($methodIds);
                    foreach($this->calculateCosts($allRates, $totals, $request,$shippingType) as $key => $cost){
                        $method = $this->_methodFactory->create()->load($key);
                        if (empty($allCosts[$key])){
                            $allCosts[$key]['cost'] = $cost['cost'];
                            $allCosts[$key]['time'] = $cost['time'];
                        }
                        else {
                            switch ($method->getSelectRate()) {
                                case self::ALGORITHM_MAX:
                                    if ($allCosts[$key]['cost'] < $cost['cost']) {
                                        $allCosts[$key]['cost'] = $cost['cost'];
                                        $allCosts[$key]['time'] = $cost['time'];
                                    }
                                    break;
                                case self::ALGORITHM_MIN:
                                    if ($allCosts[$key]['cost'] > $cost['cost']) {
                                        $allCosts[$key]['cost'] = $cost['cost'];
                                        $allCosts[$key]['time'] = $cost['time'];
                                    }
                                    break;
                                default:
                                    $allCosts[$key]['cost'] += $cost['cost'];
                                    $allCosts[$key]['time'] = $cost['time'];
                            }
                        }

                    }                                
                }  
            }            
        }
        

        return $allCosts;
    }
    
    protected function calculateCosts($allRates, $totals, $request,$shippingType)
    {
        $shippingFlatParams  =  ['country', 'state', 'city'];
        $shippingRangeParams =  ['price', 'qty', 'weight'];
        
        $minCounts = [];   // min empty values counts per method
        $results   = [];
        foreach ($allRates as $rate){
            
            $rate = $rate->getData();

            $emptyValuesCount = 0;

            if(empty($rate['shipping_type'])){
                $emptyValuesCount++;
            }
            
            foreach ($shippingFlatParams as $param){
                if (empty($rate[$param])){
                    $emptyValuesCount++;
                }                    
            }
            
            foreach ($shippingRangeParams as $param){
                if ((ceil($rate[$param . '_from'])== 0) && (ceil($rate[$param . '_to'])== 999999)) {
                    $emptyValuesCount++;
                }                   
            }

            if (empty($rate['zip_from']) && empty($rate['zip_to']) ){
                $emptyValuesCount++;
            } 

            if (!$totals['not_free_price'] && !$totals['not_free_qty'] && !$totals['not_free_weight']){
                $cost = 0;    
            } 
            else {
                $cost =  $rate['cost_base'] +  $totals['not_free_price'] * $rate['cost_percent'] / 100 + $totals['not_free_qty'] * $rate['cost_product'] + $totals['not_free_weight'] * $rate['cost_weight'];                
            }
            
            $id   = $rate['method_id'];
            if ((empty($minCounts[$id]) && empty($results[$id])) || ($minCounts[$id] > $emptyValuesCount) || (($minCounts[$id] == $emptyValuesCount) && ($cost > $results[$id]))){
                $minCounts[$id] = $emptyValuesCount;
                $results[$id]['cost']   =  $cost;
                $results[$id]['time']   =  $rate['time_delivery'];
            }
            
        }        
        return $results;
    }
    
    protected function calculateTotals($request, $ignoreVirtual, $allowFreePromo,$shippingType)
    { 
        $totals = $this->initTotals();

        $newItems = [];

        //reload child items 
        
        $isCalculateLater = [];
        
        foreach ($request->getAllItems() as $item)
		{
            // if attribute isn't load to product
            $product = $this->_productFactory->create()->load($item->getProduct()->getEntityId());
            if (($product->getShippingType() != $shippingType) && ($shippingType != 0)) 
                continue;
           
           if ($item->getParentItemId())
            continue;

            if ($ignoreVirtual && $item->getProduct()->isVirtual())
                continue;


            
            if ($item->getHasChildren()) {
                 $qty = 0;
                 $notFreeQty =0;
                 $price = 0;
                 $weight = 0;
                 $itemQty = 0;

                foreach ($item->getChildren() as $child) {
                    $itemQty = $child->getQty() * $item->getQty();
                    $qty        +=  $itemQty ;
                    $notFreeQty += ($itemQty - $this->getFreeQty($child, $allowFreePromo));
                    $price  += $child->getPrice() * $itemQty;
                    $weight += $child->getWeight() * $itemQty;
                    $totals['tax_amount']       += $child->getBaseTaxAmount() + $child->getBaseHiddenTaxAmount();
                    $totals['discount_amount']  += $child->getBaseDiscountAmount();
                }
                
                if ($item->getProductType() == 'bundle'){
                    $qty        = $item->getQty();

                    if ($item->getProduct()->getWeightType() == 1){
                        $weight  = $item->getWeight();    
                    }
                    
                    if ($item->getProduct()->getPriceType() == 1){
                        $price   = $item->getPrice();    
                    }
                    
                    if ($item->getProduct()->getSkuType() == 1){
                        $totals['tax_amount']       += $item->getBaseTaxAmount() + $item->getBaseHiddenTaxAmount();
                        $totals['discount_amount']  += $item->getBaseDiscountAmount(); 
                    }
                                        
                    $notFreeQty = ($qty - $this->getFreeQty($item, $allowFreePromo));
                    $totals['qty']              += $qty;
                    $totals['not_free_qty']     += $notFreeQty; 
                    $totals['not_free_price'] += $price;
                    $totals['not_free_weight'] += $weight;
                                                                             
                }elseif ($item->getProductType() == 'configurable'){
                    $qty     = $item->getQty();
                    $price   = $item->getPrice();
                    $weight  = $item->getWeight();
                    $notFreeQty = ($qty - $this->getFreeQty($item, $allowFreePromo));
                    $totals['qty']              += $qty;
                    $totals['not_free_qty']     += $notFreeQty; 
                    $totals['not_free_price'] += $price * $notFreeQty;
                    $totals['not_free_weight'] += $weight * $notFreeQty;
                    $totals['tax_amount']       += $item->getBaseTaxAmount() + $item->getBaseHiddenTaxAmount();
                    $totals['discount_amount']  += $item->getBaseDiscountAmount();                                                                                   
                } else { // for grouped and custom not simple products
                    $qty     = $item->getQty();
                    $price   = $item->getPrice();
                    $weight  = $item->getWeight();
                    $notFreeQty = ($qty - $this->getFreeQty($item, $allowFreePromo));
                    $totals['qty']              += $qty;
                    $totals['not_free_qty']     += $notFreeQty;
                    $totals['not_free_price'] += $price * $notFreeQty;
                    $totals['not_free_weight'] += $weight * $notFreeQty;
                }
                                

            } else {
                $qty        = $item->getQty();
                $notFreeQty = ($qty - $this->getFreeQty($item, $allowFreePromo));
                $totals['not_free_price'] += $item->getBasePrice() * $notFreeQty;
                $totals['not_free_weight'] += $item->getWeight() * $notFreeQty;
                $totals['qty']              += $qty;
                $totals['not_free_qty']     += $notFreeQty;
                $totals['tax_amount']       += $item->getBaseTaxAmount() + $item->getBaseHiddenTaxAmount();
                $totals['discount_amount']  += $item->getBaseDiscountAmount();                
            }

                               
        }// foreach   
           
        // fix magento bug
        if ($totals['qty'] != $totals['not_free_qty']) 
            $request->setFreeShipping(false);   

        $afterDiscount = $this->_scopeConfig->getValue('carriers/shippingtable/after_discount', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includingTax =  $this->_scopeConfig->getValue('carriers/shippingtable/including_tax', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
             
        if ($afterDiscount)
            $totals['not_free_price'] -= $totals['discount_amount'];   
        
        if($includingTax)
            $totals['not_free_price'] += $totals['tax_amount'];   
            
        if ($totals['not_free_price'] < 0)
            $totals['not_free_price'] = 0;
        
        if ($request->getFreeShipping() && $allowFreePromo)
            $totals['not_free_price'] = $totals['not_free_weight'] = $totals['not_free_qty'] = 0;     

        return $totals;       
    }
    
    public function getFreeQty($item, $allowFreePromo)
    {
        $freeQty = 0;

        if ($item->getFreeShipping() && $allowFreePromo)
            $freeQty = ((is_numeric($item->getFreeShipping())) && ($item->getFreeShipping() <= $item->getQty())) ? $item->getFreeShipping() : $item->getQty();
            
        return $freeQty;        
    }
    
    public function import($methodId, $fileName)
    {
        $err = []; 
        
        $fp = fopen($fileName, 'r');
        if (!$fp){
            $err[] = __('Can not open file %s .', $fileName);  
            return $err;
        }
        $methodId = intval($methodId);
        if (!$methodId){
            $err[] = __('Specify a valid method ID.');  
            return $err;
        }
        
        $countryCodes = $this->getCountries();
        $stateCodes   = $this->getStates();
        $countryNames = $this->getCountriesName();
        $stateNames   = $this->getStatesName();
        $typeLabels   = $this->_helper->getTypes();
                    
        $data = [];
        $dataIndex = 0;
        
        $currLineNum  = 0;
        while (($line = fgetcsv($fp, self::MAX_LINE_LENGTH, ',', '"')) !== false) {
            $currLineNum++;

            if (count($line) == 1)
            {
                continue;
            }

            if (count($line) != self::COL_NUMS){ 
               $err[] = 'Line #' . $currLineNum . ': warning, expected number of columns is ' . self::COL_NUMS;
               if (count($line) > self::COL_NUMS)
               {
                   for ($i = 0; $i < count($line) - self::COL_NUMS; $i++){
                        unset($line[self::COL_NUMS + $i]);
                   }
               }

                if (count($line) < self::COL_NUMS)
                {
                    for ($i = 0; $i <  self::COL_NUMS - count($line); $i++){
                        $line[count($line) + $i] = 0;
                    }
                }
            }
            
            for ($i = 0; $i < self::COL_NUMS; $i++) {
               $line[$i] = str_replace(array("\r", "\n", "\t", "\\" ,'"', "'", "*"), '', $line[$i]);
            }

            $countries = array('');
            if ($line[self::COUNTRY]){
                $countries = explode(',', $line[self::COUNTRY]);
            } else {
                $line[self::COUNTRY] = '0';
            }
            $states = array('');
            if ($line[self::STATE]){
                $states = explode(',', $line[self::STATE]);
            }

            $types = array('');
            if ($line[self::SHIPPING_TYPE]){
                $types = explode(',', $line[self::SHIPPING_TYPE]);
            }

            $zips = array('');
            if ($line[self::ZIP_FROM]){
                $zips = explode(',', $line[self::ZIP_FROM]);
            }

            if(!$line[self::PRICE_TO]) $line[self::PRICE_TO] =  99999999;
            if(!$line[self::WEIGHT_TO]) $line[self::WEIGHT_TO] =  99999999;
            if(!$line[self::QTY_TO]) $line[self::QTY_TO] =  99999999;
            
            foreach ($types as $type){
               if ($type == 'All'){
                    $type = 0;   
                }
                if ($type && empty($typeLabels[$type])) {
                    if (in_array($type, $typeLabels)){
                        $typeLabels[$type] = array_search($type, $typeLabels);   
                    }  else {
                        $err[] = 'Line #' . $currLineNum . ': invalid type code ' . $type;
                        continue;                       
                    }

                }
                $line[self::SHIPPING_TYPE] = $type ? $typeLabels[$type] : '';
            }
            
            foreach ($countries as $country){
               if ($country == 'All'){
                    $country = 0;   
                }
                
                if ($country && empty($countryCodes[$country])) {
                    if (in_array($country, $countryNames)){
                        $countryCodes[$country] = array_search($country, $countryNames);   
                    }  else {
                        $err[] = 'Line #' . $currLineNum . ': invalid country code ' . $country;
                        continue;                       
                    }

                }
                $line[self::COUNTRY] = $country ? $countryCodes[$country] : '0';

                foreach ($states as $state){
                    
                    if ($state == 'All'){
                        $state = '';  
                    }
                                        
                    if ($state && empty($stateCodes[$state][$country])) {
                        if (in_array($state, $stateNames)){
                            $stateCodes[$state][$country] = array_search($state, $stateNames);    
                        } else {  
                            $err[] = 'Line #' . $currLineNum . ': invalid state code ' . $state;
                            continue;                            
                        }                    

                    }
                    $line[self::STATE] = $state ? $stateCodes[$state][$country] : '';
                    
                    foreach ($zips as $zip){
                        $line[self::ZIP_FROM] = $zip;
                        
                        
                        $data[$dataIndex] = $line;
                        $dataIndex++;

                        if ($dataIndex > self::BATCH_SIZE){
                            $errText = $this->getResource()->batchInsert($methodId, $data);
                            if ($errText){
                                $err[] = 'Line #' . $currLineNum . ': duplicated conditions before this line have been skipped';
                            }
                            $data = array();
                            $dataIndex = 0;
                        }
                    }                    
                }// states  
            }// countries 
        } // end while read  
        fclose($fp);
        
        if ($dataIndex){
            $errText = $this->getResource()->batchInsert($methodId, $data);

            if ($errText){
                $err[] = 'Line #' . $currLineNum . ': duplicated conditions before this line have been skipped';
            }
        }
        
        return $err;
    }
    
    public function getCountries()
    {
        $hash = [];
        
        $collection = $this->_countryCollection->create();
        foreach ($collection as $item)
		{
            $hash[$item['iso3_code']] = $item['country_id'];
            $hash[$item['iso2_code']] = $item['country_id'];
        }
        
        return $hash;
    }
    
    public function getStates()
    {
        $hash = [];
        
        $collection = $this->_regionCollection->create();
        foreach ($collection as $state)
		{
            $hash[$state['code']][$state['country_id']] = $state['region_id'];
        }

        return $hash;
    }
    public function getCountriesName()
    {
        $hash = [];
        $collection = $this->_countryCollection->create();
        foreach ($collection as $item)
		{
            $country_name = $this->_localeLists->getCountryTranslation($item['iso2_code']);
            $hash[$item['country_id']] = $country_name;
                
        }
        return $hash;
    }
    
    
    public function getStatesName()
    {
        $hash = [];
        
        $collection = $this->_regionCollection->create();
        $countryHash = $this->getCountriesName();
        foreach ($collection as $state)
		{
            $string = $countryHash[$state['country_id']].'/'.$state['default_name'];
            $hash[$state['region_id']] =  $string;  
        } 
        return $hash;               
    }
        
    public function initTotals()
    {
        $totals = [
            'not_free_price'     => 0,
            'not_free_weight'    => 0,
            'qty'                => 0,
            'not_free_qty'       => 0,
            'tax_amount'         => 0,
            'discount_amount'    => 0,
        ];        
        return $totals;
    } 
    
    public function deleteBy($methodId)
    {
        return $this->getResource()->deleteBy($methodId);   
    }
}
