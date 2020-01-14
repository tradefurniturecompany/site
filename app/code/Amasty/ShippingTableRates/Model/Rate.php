<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Model;

use Amasty\ShippingTableRates\Api\Data\RateInterface;
use Amasty\ShippingTableRates\Model\Rate\Import\RateImportService;
use Amasty\ShippingTableRates\Model\ResourceModel\Method\Collection as MethodCollection;
use Magento\Framework\Model\AbstractModel;
use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Rate Data of Shipping Method.
 *  Shipping Method can have set of Rates
 */
class Rate extends AbstractModel implements RateInterface
{
    const ALGORITHM_SUM = 0;

    const ALGORITHM_MAX = 1;

    const ALGORITHM_MIN = 2;

    const MAX_VALUE = 99999999;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    protected $_shippingTypes = [];

    protected $_existingShippingTypes = [];

    /**
     * @var ResourceModel\Rate\CollectionFactory
     */
    private $rateCollectionFactory;

    /**
     * @var ResourceModel\Method\CollectionFactory
     */
    private $methodCollectionFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Rate\ItemsTotalCalculator
     */
    private $itemsTotalCalculator;

    /**
     * @var Rate\ItemValidator
     */
    private $itemValidator;

    protected function _construct()
    {
        $this->_init(\Amasty\ShippingTableRates\Model\ResourceModel\Rate::class);
    }

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Model\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Amasty\ShippingTableRates\Model\ResourceModel\Rate\CollectionFactory $rateCollectionFactory,
        \Amasty\ShippingTableRates\Model\ResourceModel\Method\CollectionFactory $methodCollectionFactory,
        ConfigProvider $configProvider,
        Rate\ItemsTotalCalculator $itemsTotalCalculator,
        Rate\ItemValidator $itemValidator
    ) {
        $this->productRepository = $productRepository;
        $this->rateCollectionFactory = $rateCollectionFactory;
        $this->methodCollectionFactory = $methodCollectionFactory;
        $this->configProvider = $configProvider;
        $this->itemsTotalCalculator = $itemsTotalCalculator;
        $this->itemValidator = $itemValidator;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * @param int $methodId
     * @deprecated since 1.5.1 @see \Amasty\ShippingTableRates\Model\ResourceModel\Rate::deleteBy
     */
    public function deleteBy($methodId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objectManager->get(\Amasty\ShippingTableRates\Model\ResourceModel\Rate::class)
            ->deleteBy($methodId);
    }

    /**
     * Method moved to a separate cass
     * @param int $methodId
     * @param string $fileName
     *
     * @return array
     * @deprecated since 1.5.1 @see \Amasty\ShippingTableRates\Model\Rate\Import\RateImportService::import
     */
    public function import($methodId, $fileName)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->get(RateImportService::class)
            ->import($methodId, $fileName);
    }

    /**
     * @param array $data
     * @param int $methodId
     * @param int $currLineNum
     * @param array $err
     *
     * @return array
     * @deprecated since 1.5.1 @see \Amasty\ShippingTableRates\Model\Rate\Import\RateImportService::returnErrors
     */
    public function returnErrors($data, $methodId, $currLineNum, $err)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->get(RateImportService::class)
            ->returnErrors($data, $methodId, $currLineNum, $err);
    }

    /**
     * @param RateRequest $request
     * @param MethodCollection $collection
     *
     * @return array
     */
    public function findBy(RateRequest $request, MethodCollection $collection)
    {
        if (!$request->getAllItems()) {
            return [];
        }

        if ($collection->getSize() == 0) {
            return [];
        }

        $methodIds = [];
        foreach ($collection as $method) {
            $methodIds[] = $method->getId();
        }

        // calculate price and weight
        $allowFreePromo = $this->configProvider->isPromoAllowed();

        /** @var \Magento\Quote\Model\Quote\Item[] $items */
        $items = $request->getAllItems();

        $collectedTypes = [];
        $isFreeShipping = 0;

        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }

            if ($this->itemValidator->isShouldProcessChildren($item)) {
                foreach ($item->getChildren() as $child) {
                    $this->getShippingTypes($child);
                }
            } else {
                $this->getShippingTypes($item);
            }
            $address = $item->getAddress();

            if ($allowFreePromo && $address->getFreeShipping() === true) {
                $isFreeShipping = 1;
            }
        }

        $this->_shippingTypes = $this->_existingShippingTypes;
        $this->_shippingTypes[] = 0;

        $this->_shippingTypes = array_unique($this->_shippingTypes);
        $this->_existingShippingTypes = array_unique($this->_existingShippingTypes);

        $allCosts = [];
        $ratesTypes = [];

        /** @var \Amasty\ShippingTableRates\Model\ResourceModel\Rate\Collection $rateCollection */
        $rateCollection = $this->rateCollectionFactory->create();
        $ratesData = $rateCollection->getRatesWithFilters($methodIds, true);

        foreach ($ratesData as $singleRate) {
            $ratesTypes[$singleRate['method_id']][] = $singleRate['shipping_type'];
        }

        $rateCollection->reset();

        $intersectTypes = [];
        $freeTypes = [];
        /** @var MethodCollection $methodCollection */
        $methodCollection = $this->methodCollectionFactory->create();

        foreach ($ratesTypes as $key => $value) {
            $intersectTypes[$key] = array_intersect($this->_shippingTypes, $value);
            arsort($intersectTypes[$key]);
            $methodIds = [$key];
            $allTotals = $this->itemsTotalCalculator->execute($request, '0');
            /** @var \Amasty\ShippingTableRates\Model\Method $method */
            $method = $methodCollection->getNewEmptyItem();
            $method->load($key);

            foreach ($intersectTypes[$key] as $shippingType) {
                $totals = $this->itemsTotalCalculator->execute($request, $shippingType);

                if ($allTotals['qty'] > 0
                    && (!$this->configProvider->getDontSplit() || $allTotals['qty'] == $totals['qty'])
                ) {

                    if ($shippingType == 0) {
                        $totals = $allTotals;
                    }

                    /**
                     * avoid php opcache 7.0.33 bug
                     */
                    $allTotals['not_free_price'] = $allTotals['not_free_price'] - $totals['not_free_price'];
                    $allTotals['not_free_weight'] = $allTotals['not_free_weight'] - $totals['not_free_weight'];
                    $allTotals['not_free_qty'] = $allTotals['not_free_qty'] - $totals['not_free_qty'];
                    $allTotals['qty'] = $allTotals['qty'] - $totals['qty'];

                    /** @var \Amasty\ShippingTableRates\Model\ResourceModel\Rate\Collection $rateCollection */
                    $rateCollection = $this->rateCollectionFactory->create();
                    $ratesData = $rateCollection->getRatesWithFilters(
                        $methodIds,
                        false,
                        [$request, $totals, $shippingType, $allowFreePromo]
                    );
                    $rateCollection->reset();

                    foreach ($this->calculateCosts($ratesData, $totals, $request, $shippingType) as $key => $cost) {
                        if (!($totals['not_free_qty'] > 0) && !($totals['qty'] > 0)) {
                            continue;
                        }

                        if (!($totals['not_free_qty'] > 0)) {
                            $cost['cost'] = 0;
                        }

                        if (empty($allCosts[$key])) {
                            $allCosts[$key]['cost'] = $cost['cost'];
                            $allCosts[$key]['time'] = $cost['time'];
                            $allCosts[$key]['name_delivery'] = $cost['name_delivery'];

                        } else {
                            $allCosts = $this->_setCostTime($method, $allCosts, $key, $cost);
                        }
                        $collectedTypes[$key][] = $shippingType;
                        $freeTypes[$key] = $method->getFreeTypes();
                    }
                }
            }
        }

        $allCosts = $this->_unsetUnnecessaryCosts($allCosts, $collectedTypes, $freeTypes);

        $minRates = $methodCollection->hashMinRate();
        $maxRates = $methodCollection->hashMaxRate();

        $allCosts = $this->_includeMinMaxRates($allCosts, $maxRates, $minRates);
        $allCosts = $this->applyFreeShipping($allCosts, $isFreeShipping);

        return $allCosts;
    }

    /**
     * @param \Amasty\ShippingTableRates\Model\Rate $method
     * @param array $allCosts
     * @param int $key
     * @param array $cost
     *
     * @return array
     */
    protected function _setCostTime($method, $allCosts, $key, $cost)
    {
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

        return $allCosts;
    }

    /**
     * @param array $allCosts
     * @param array $maxRates
     * @param array $minRates
     *
     * @return array
     */
    protected function _includeMinMaxRates($allCosts, $maxRates, $minRates)
    {
        foreach ($allCosts as $key => $rate) {
            if ($maxRates[$key] != '0.00' && $maxRates[$key] < $rate['cost']) {
                $allCosts[$key]['cost'] = $maxRates[$key];
            }

            if ($minRates[$key] != '0.00' && $minRates[$key] > $rate['cost']) {
                $allCosts[$key]['cost'] = $minRates[$key];
            }
        }

        return $allCosts;
    }

    /**
     * @param array $allCosts
     * @param int $isFreeShipping
     *
     * @return array
     */
    protected function applyFreeShipping($allCosts, $isFreeShipping)
    {
        if ($isFreeShipping) {
            foreach ($allCosts as $key => $rate) {
                $allCosts[$key]['cost'] = 0;
            }
        }

        return $allCosts;
    }

    /**
     * @param array $allCosts
     * @param array $collectedTypes
     * @param array $freeTypes
     *
     * @return array
     */
    protected function _unsetUnnecessaryCosts($allCosts, $collectedTypes, $freeTypes)
    {
        //do not show method if quote has "unsuitable" items
        foreach ($allCosts as $key => $cost) {
            //1.if the method contains rate with type == All
            if (in_array('0', $collectedTypes[$key])) {
                continue;
            }
            //2.if the method rates contain types for every items in quote
            $extraTypes = array_diff($this->_existingShippingTypes, $collectedTypes[$key]);
            if (!$extraTypes) {
                continue;
            }
            //3.if the method free types contain types for every item didn't pass (2)
            if (!array_diff($extraTypes, $freeTypes[$key])) {
                continue;
            }

            //else — do not show the method;
            unset($allCosts[$key]);
        }

        return $allCosts;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     */
    protected function getShippingTypes($item)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->getById($item->getProductId());

        if ($product->getAmShippingType()) {
            $this->_existingShippingTypes[] = $product->getAmShippingType();
        } else {
            $this->_existingShippingTypes[] = 0;
        }
    }

    /**
     * @param array $allRates
     * @param array $totals
     * @param RateRequest $request
     * @param int $shippingType
     *
     * @return array
     */
    protected function calculateCosts($allRates, $totals, $request, $shippingType)
    {
        $shippingFlatParams = ['country', 'state'];
        $shippingRangeParams = ['price', 'qty', 'weight'];

        $minCounts = [];   // min empty values counts per method
        $results = [];
        foreach ($allRates as $rate) {
            $emptyValuesCount = 0;

            if (empty($rate['shipping_type'])) {
                $emptyValuesCount++;
            }

            foreach ($shippingFlatParams as $param) {
                if (empty($rate[$param])) {
                    $emptyValuesCount++;
                }
            }

            foreach ($shippingRangeParams as $param) {
                if ((ceil($rate[$param . '_from']) == 0) && (ceil($rate[$param . '_to']) == self::MAX_VALUE)) {
                    $emptyValuesCount++;
                }
            }

            if (empty($rate['zip_from']) && empty($rate['zip_to'])) {
                $emptyValuesCount++;
            }

            if (!$totals['not_free_price'] && !$totals['not_free_qty'] && !$totals['not_free_weight']) {
                $cost = 0;
            } else {
                $cost = $rate['cost_base'] + ($totals['not_free_price'] * $rate['cost_percent'] / 100)
                    + ($totals['not_free_qty'] * $rate['cost_product'])
                    + ($totals['not_free_weight'] * $rate['cost_weight']);
            }
            $id = $rate['method_id'];

            if ((empty($minCounts[$id]) && empty($results[$id])) || ($minCounts[$id] > $emptyValuesCount)
                || (($minCounts[$id] == $emptyValuesCount) && ($cost > $results[$id]))
            ) {
                $minCounts[$id] = $emptyValuesCount;
                $results[$id]['cost'] = $cost;
                $results[$id]['time'] = $rate['time_delivery'];
                $results[$id]['shipping_type'] = $rate['shipping_type'];
                $results[$id]['name_delivery'] = $rate['name_delivery'];
            }

        }

        return $results;
    }

    /**
     * @inheritdoc
     */
    public function getMethodId()
    {
        return $this->_getData(RateInterface::METHOD_ID);
    }

    /**
     * @inheritdoc
     */
    public function setMethodId($methodId)
    {
        $this->setData(RateInterface::METHOD_ID, $methodId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCountry()
    {
        return $this->_getData(RateInterface::COUNTRY);
    }

    /**
     * @inheritdoc
     */
    public function setCountry($country)
    {
        $this->setData(RateInterface::COUNTRY, $country);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getState()
    {
        return $this->_getData(RateInterface::STATE);
    }

    /**
     * @inheritdoc
     */
    public function setState($state)
    {
        $this->setData(RateInterface::STATE, $state);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getZipFrom()
    {
        return $this->_getData(RateInterface::ZIP_FROM);
    }

    /**
     * @inheritdoc
     */
    public function setZipFrom($zipFrom)
    {
        $this->setData(RateInterface::ZIP_FROM, $zipFrom);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getZipTo()
    {
        return $this->_getData(RateInterface::ZIP_TO);
    }

    /**
     * @inheritdoc
     */
    public function setZipTo($zipTo)
    {
        $this->setData(RateInterface::ZIP_TO, $zipTo);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPriceFrom()
    {
        return $this->_getData(RateInterface::PRICE_FROM);
    }

    /**
     * @inheritdoc
     */
    public function setPriceFrom($priceFrom)
    {
        $this->setData(RateInterface::PRICE_FROM, $priceFrom);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPriceTo()
    {
        return $this->_getData(RateInterface::PRICE_TO);
    }

    /**
     * @inheritdoc
     */
    public function setPriceTo($priceTo)
    {
        $this->setData(RateInterface::PRICE_TO, $priceTo);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWeightFrom()
    {
        return $this->_getData(RateInterface::WEIGHT_FROM);
    }

    /**
     * @inheritdoc
     */
    public function setWeightFrom($weightFrom)
    {
        $this->setData(RateInterface::WEIGHT_FROM, $weightFrom);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWeightTo()
    {
        return $this->_getData(RateInterface::WEIGHT_TO);
    }

    /**
     * @inheritdoc
     */
    public function setWeightTo($weightTo)
    {
        $this->setData(RateInterface::WEIGHT_TO, $weightTo);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getQtyFrom()
    {
        return $this->_getData(RateInterface::QTY_FROM);
    }

    /**
     * @inheritdoc
     */
    public function setQtyFrom($qtyFrom)
    {
        $this->setData(RateInterface::QTY_FROM, $qtyFrom);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getQtyTo()
    {
        return $this->_getData(RateInterface::QTY_TO);
    }

    /**
     * @inheritdoc
     */
    public function setQtyTo($qtyTo)
    {
        $this->setData(RateInterface::QTY_TO, $qtyTo);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getShippingType()
    {
        return $this->_getData(RateInterface::SHIPPING_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setShippingType($shippingType)
    {
        $this->setData(RateInterface::SHIPPING_TYPE, $shippingType);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCostBase()
    {
        return $this->_getData(RateInterface::COST_BASE);
    }

    /**
     * @inheritdoc
     */
    public function setCostBase($costBase)
    {
        $this->setData(RateInterface::COST_BASE, $costBase);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCostPercent()
    {
        return $this->_getData(RateInterface::COST_PERCENT);
    }

    /**
     * @inheritdoc
     */
    public function setCostPercent($costPercent)
    {
        $this->setData(RateInterface::COST_PERCENT, $costPercent);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCostProduct()
    {
        return $this->_getData(RateInterface::COST_PRODUCT);
    }

    /**
     * @inheritdoc
     */
    public function setCostProduct($costProduct)
    {
        $this->setData(RateInterface::COST_PRODUCT, $costProduct);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCostWeight()
    {
        return $this->_getData(RateInterface::COST_WEIGHT);
    }

    /**
     * @inheritdoc
     */
    public function setCostWeight($costWeight)
    {
        $this->setData(RateInterface::COST_WEIGHT, $costWeight);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTimeDelivery()
    {
        return $this->_getData(RateInterface::TIME_DELIVERY);
    }

    /**
     * @inheritdoc
     */
    public function setTimeDelivery($timeDelivery)
    {
        $this->setData(RateInterface::TIME_DELIVERY, $timeDelivery);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNumZipFrom()
    {
        return $this->_getData(RateInterface::NUM_ZIP_FROM);
    }

    /**
     * @inheritdoc
     */
    public function setNumZipFrom($numZipFrom)
    {
        $this->setData(RateInterface::NUM_ZIP_FROM, $numZipFrom);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNumZipTo()
    {
        return $this->_getData(RateInterface::NUM_ZIP_TO);
    }

    /**
     * @inheritdoc
     */
    public function setNumZipTo($numZipTo)
    {
        $this->setData(RateInterface::NUM_ZIP_TO, $numZipTo);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCity()
    {
        return $this->_getData(RateInterface::CITY);
    }

    /**
     * @inheritdoc
     */
    public function setCity($city)
    {
        $this->setData(RateInterface::CITY, $city);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNameDelivery()
    {
        return $this->_getData(RateInterface::NAME_DELIVERY);
    }

    /**
     * @inheritdoc
     */
    public function setNameDelivery($nameDelivery)
    {
        $this->setData(RateInterface::NAME_DELIVERY, $nameDelivery);

        return $this;
    }
}
