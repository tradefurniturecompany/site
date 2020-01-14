<?php
namespace Magesales\Shippingtable\Model\Carrier;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
class Table extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
    protected $_code = 'shippingtable';
	protected $_rateErrorFactory;
	protected $_rateResultFactory;
	protected $_rateMethodFactory;
	protected $_shippingTableFactory;
	protected $_shippingTableRate;
	
	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
		\Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
		\Magesales\Shippingtable\Model\ResourceModel\Method\CollectionFactory $shippingTableFactory,
		\Magesales\Shippingtable\Model\Rate $shippingTableRate,
		array $data = [])
	{
		$this->_rateErrorFactory = $rateErrorFactory;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
		$this->_shippingTableFactory = $shippingTableFactory;
		$this->_shippingTableRate = $shippingTableRate;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
	}
    /**
     * Collect rates for this shipping method based on information in $request
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request) 
    {
        if (!$this->getConfigData('active')) {
            return false;
        }

        $result = $this->_rateResultFactory->create();

        $collection = $this->_shippingTableFactory->create()
            ->addFieldToFilter('is_active', 1)
            ->addStoreFilter($request->getStoreId())
            ->addCustomerGroupFilter($this->getCustomerGroupId($request))
            ->setOrder('pos');
                            
        $rates = $this->_shippingTableRate->findBy($request, $collection);    
        
        $countOfRates = 0; 
        foreach ($collection as $customMethod){
            
            // create new instance of method rate
            $method = $this->_rateMethodFactory->create();
    
            // record carrier information
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            if (isset($rates[$customMethod->getId()]['cost']))
            {
                // record method information
                $method->setMethod($this->_code . $customMethod->getId());
                $methodTitle = __($customMethod->getName());
                $methodTitle = str_replace('{day}', $rates[$customMethod->getId()]['time'], $methodTitle);
                $method->setMethodTitle($methodTitle);

                $method->setCost($rates[$customMethod->getId()]['cost']);
                $method->setPrice($rates[$customMethod->getId()]['cost']);

                // add this rate to the result
                $result->append($method);
                $countOfRates++;
            }

        }
        
        if (($countOfRates == 0) && ($this->getConfigData('showmethod') == 1))
		{
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        }        
        
        return $result;
    } 


    public function getAllowedMethods()
    {
        $collection = $this->_shippingTableFactory->create()
                ->addFieldToFilter('is_active', 1)
                ->setOrder('pos');
        $arr = [];
        foreach ($collection as $method)
		{
            $methodCode = 'shippingtable'.$method->getMethodId();
            $arr[$methodCode] = $method->getName();    
        }  
                
        return $arr;
    }
    
    public function getCustomerGroupId($request)
    {
        $allItems = $request->getAllItems();
		
        if (!$allItems){
            return 0;
        }
        foreach ($allItems as $item)
        {
			return $item->getProduct()->getCustomerGroupId();             
        }

    }
}
