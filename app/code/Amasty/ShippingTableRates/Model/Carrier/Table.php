<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Model\Carrier;

use Magento\Framework\App\Area;
use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Shipping Table Rate implementation
 */
class Table extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    const VARIABLE_DAY = '{day}';
    const VARIABLE_DLIVERY_NAME = '{name}';

    protected $_code = 'amstrates';
    protected $_isFixed = true;
    protected $_rateResultFactory;
    protected $_rateMethodFactory;
    /**
     * @var \Amasty\ShippingTableRates\Model\ResourceModel\Label\CollectionFactory
     */
    private $labelCollectionFactory;
    /**
     * @var \Amasty\ShippingTableRates\Model\ResourceModel\Method\CollectionFactory
     */
    private $methodCollectionFactory;
    /**
     * @var \Amasty\ShippingTableRates\Model\RateFactory
     */
    private $rateFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Amasty\ShippingTableRates\Helper\Data
     */
    private $helperData;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Amasty\ShippingTableRates\Model\ResourceModel\Label\CollectionFactory $labelCollectionFactory,
        \Amasty\ShippingTableRates\Model\ResourceModel\Method\CollectionFactory $methodCollectionFactory,
        \Amasty\ShippingTableRates\Model\RateFactory $rateFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\ShippingTableRates\Helper\Data $helperData,
        \Magento\Framework\App\State $state,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->labelCollectionFactory = $labelCollectionFactory;
        $this->methodCollectionFactory = $methodCollectionFactory;
        $this->rateFactory = $rateFactory;
        $this->storeManager = $storeManager;
        $this->helperData = $helperData;
        $this->state = $state;
    }

    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigData('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        /** @var \Amasty\ShippingTableRates\Model\ResourceModel\Label\Collection $customLabel */
        $customLabel = $this->labelCollectionFactory->create();
        /** @var \Amasty\ShippingTableRates\Model\ResourceModel\Method\Collection $methodCollection */
        $methodCollection = $this->methodCollectionFactory->create();

        $storeId = $this->state->getAreaCode() == Area::AREA_ADMINHTML
            ? $this->getStoreIdFromQuoteItem($request) : $this->storeManager->getStore()->getId();
        $methodCollection
            ->addFieldToFilter('is_active', 1)
            ->addStoreFilter($storeId)
            ->addCustomerGroupFilter($this->getCustomerGroupId($request));

        /** @var \Amasty\ShippingTableRates\Model\Rate $modelRate */
        $modelRate = $this->rateFactory->create();
        $rates = $modelRate->findBy($request, $methodCollection);
        $countOfRates = 0;
        foreach ($methodCollection as $customMethod) {
            $customLabelData = $customLabel->addFiltersByMethodIdStoreId($customMethod->getId(), $storeId)
                ->getLastItem();
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();
            // record carrier information
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            if (isset($rates[$customMethod->getId()]['cost'])) {
                // record method information
                $method->setMethod($this->_code . $customMethod->getId());
                $label = $this->helperData->escapeHtml($customLabelData->getLabel());

                if ($label === null || $label === '') {
                    $methodTitle = __($customMethod->getName());
                } else {
                    $methodTitle = __($label);
                }
                $methodTitle = str_replace(static::VARIABLE_DAY, $rates[$customMethod->getId()]['time'], $methodTitle);
                $methodTitle = str_replace(
                    static::VARIABLE_DLIVERY_NAME,
                    $rates[$customMethod->getId()]['name_delivery'],
                    $methodTitle
                );
                $method->setMethodTitle($methodTitle);

                $method->setCost($rates[$customMethod->getId()]['cost']);
                $method->setPrice($rates[$customMethod->getId()]['cost']);

                $method->setPos($customMethod->getPos());

                // add this rate to the result
                $result->append($method);
                $countOfRates++;
            }
        }

        if (($countOfRates == 0) && ($this->getConfigData('showmethod') == 1)) {
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
        /** @var \Amasty\ShippingTableRates\Model\ResourceModel\Method\Collection $collection */
        $collection = $this->methodCollectionFactory->create();
        $collection
            ->addFieldToFilter('is_active', 1);
        $arr = [];
        /** @var \Amasty\ShippingTableRates\Model\Method $method */
        foreach ($collection->getItems() as $method) {
            $methodCode = 'amstrates' . $method->getId();
            $arr[$methodCode] = $method->getName();
        }

        return $arr;
    }

    /**
     * @param $request
     * @return int
     */
    public function getCustomerGroupId($request)
    {
        $allItems = $request->getAllItems();

        if (!$allItems) {
            return 0;
        }

        foreach ($allItems as $item) {
            return $item->getProduct()->getCustomerGroupId();
        }
    }

    /**
     * @param $request
     * @return int
     */
    public function getStoreIdFromQuoteItem($request)
    {
        $allItems = $request->getAllItems();

        if (!$allItems) {
            return (int)true;
        }

        foreach ($allItems as $item) {
            return $item->getStoreId();
        }
    }
}
