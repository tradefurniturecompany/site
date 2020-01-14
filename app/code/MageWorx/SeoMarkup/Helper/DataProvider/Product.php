<?php
/**
 * Copyright © 2019 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Helper\DataProvider;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\DateTime;

class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * @var \MageWorx\SeoMarkup\Helper\Product
     */
    protected $helperData;

    /**
     *
     * @var \Magento\Catalog\Block\Product\ImageBuilder
     */
    protected $imageBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category
     */
    protected $resourceCategory;

    /**
     * Review model factory
     *
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     *
     * @var array|null
     */
    protected $ratingData;

    /**
     *
     * @var null|string
     */
    protected $categoryName;

    /**
     *
     * @var array
     */
    protected $attributeValues = [];

    /**
     *
     * @var string
     */
    protected $conditionValue;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * Product constructor.
     *
     * @param \MageWorx\SeoMarkup\Helper\Product $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Category $resourceCategory
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Framework\App\Helper\Context $context
     * @param TimezoneInterface $timezone
     * @param DateTime $dateTime
     */
    public function __construct(
        \MageWorx\SeoMarkup\Helper\Product $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Category $resourceCategory,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Framework\App\Helper\Context $context,
        TimezoneInterface $timezone,
        DateTime $dateTime
    ) {
        $this->helperData       = $helperData;
        $this->storeManager     = $storeManager;
        $this->imageBuilder     = $imageBuilder;
        $this->registry         = $registry;
        $this->resourceCategory = $resourceCategory;
        $this->reviewFactory    = $reviewFactory;
        $this->timezone         = $timezone;
        $this->dateTime         = $dateTime;
        parent::__construct($context);
    }

    /**
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCode();
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getDescriptionValue($product)
    {
        $attributeCode = $this->helperData->getDescriptionCode();

        if ($attributeCode) {
            $description = $this->getAttributeValueByCode($product, $attributeCode);
            return $description;
        }
        return $product->getShortDescription();
    }

    /**
     * Retrieve attribute value by attribute code
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $attributeCode
     * @return string|array
     */
    public function getAttributeValueByCode($product, $attributeCode)
    {
        if (array_key_exists($attributeCode, $this->attributeValues)) {
            return $this->attributeValues[$attributeCode];
        }

        $value = $product->getData($attributeCode);

        $tempValue = '';

        if (!is_array($value)) {
            $attribute = $product->getResource()->getAttribute($attributeCode);
            if ($attribute) {
                $attribute->setStoreId($product->getStoreId());
                $tempValue = $attribute->setStoreId($product->getStoreId())->getSource()->getOptionText($value);
            }
        }

        if ($tempValue) {
            $value = $tempValue;
        }

        if (!$value) {
            if ($product->getTypeId() == 'configurable') {
                $productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);

                $attributeOptions = [];
                foreach ($productAttributeOptions as $productAttribute) {
                    if ($productAttribute['attribute_code'] != $attributeCode) {
                        continue;
                    }
                    foreach ($productAttribute['values'] as $attribute) {
                        $attributeOptions[] = $attribute['store_label'];
                    }
                }
                if (count($attributeOptions) == 1) {
                    $value = array_shift($attributeOptions);
                }
            } else {
                $value = $product->getData($attributeCode);
            }
        }

        $finalValue = is_array($value) ? array_map('trim', array_filter($value)) : trim($value);

        $this->attributeValues[$attributeCode] = $finalValue;
        return $finalValue;
    }

    /**
     * @todo Retrive product canonical URL from SeoBase or Magento Canonical URL.
     * @return string
     */
    public function getProductCanonicalUrl($product)
    {
        if (!empty($this->productCanonicalUrl)) {
            return $this->productCanonicalUrl;
        }
        $this->productCanonicalUrl = $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]);
        return $this->productCanonicalUrl;
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getConditionValue($product)
    {
        if (!is_null($this->conditionValue)) {
            return $this->conditionValue;
        }

        $attributeCode      = $this->helperData->getConditionCode();
        $conditionByDefault = $this->helperData->getConditionDefaultValue();

        if ($attributeCode) {
            $conditionValue = $this->getAttributeValueByCode($product, $attributeCode);

            switch ($conditionValue) {
                case $this->helperData->getConditionValueForNew():
                    $conditionValue = "NewCondition";
                    break;
                case $this->helperData->getConditionValueForUsed():
                    $conditionValue = "UsedCondition";
                    break;
                case $this->helperData->getConditionValueForRefurbished():
                    $conditionValue  = "RefurbishedCondition";
                    break;
                case $this->helperData->getConditionValueForDamaged():
                    $conditionValue = "DamagedCondition";
                    break;
                default:
                    if ($conditionByDefault) {
                        $conditionValue = $conditionByDefault;
                    }
                    break;
            }
        } elseif ($conditionByDefault) {
             $conditionValue = $conditionByDefault;
        }

        $conditionValue = !empty($conditionValue) ? $conditionValue : false;
        $this->conditionValue = $conditionValue;

        return $this->conditionValue;
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param boolean $useMagentoBestRating
     * @return array
     */
    public function getAggregateRatingData($product, $useMagentoBestRating = true)
    {
        if (!is_null($this->ratingData)) {
            return $this->ratingData;
        }

        if (!$product->getRatingSummary()) {
            $this->reviewFactory->create()->getEntitySummary($product, $this->storeManager->getStore()->getId());
        }

        $reviewDataObject = $product->getRatingSummary();

        if (!is_object($reviewDataObject) || (is_object($reviewDataObject) && !$reviewDataObject->getData())) {
            $this->ratingData = [];
            return $this->ratingData;
        }

        $reviewData = $reviewDataObject->getData();

        if (empty($reviewData['reviews_count'])) {
            $this->ratingData = [];
            return $this->ratingData;
        }

        $reviewCount  = $reviewData['reviews_count'];
        $reviewRating = $reviewData['rating_summary'];

        $data = [];

        if ($this->helperData->getBestRating() && !$useMagentoBestRating) {
            $bestRating = $this->helperData->getBestRating();
            $rating     = round(($reviewRating / (100 / $bestRating)), 1);
        } else {
            $bestRating = 100;
            $rating     = $reviewRating;
        }

        $data['ratingValue'] = $rating;
        $data['reviewCount'] = $reviewCount;
        $data['bestRating']  = $bestRating;
        $data['worstRating'] = 0;

        $this->ratingData = $data;

        return $this->ratingData;
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getColorValue($product)
    {
        if ($this->helperData->isColorEnabled()) {
            $attributeCode = $this->helperData->getColorCode();
            if ($attributeCode) {
                return $this->getAttributeValueByCode($product, $attributeCode);
            }
        }
        return null;
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getBrandValue($product)
    {
        if ($this->helperData->isBrandEnabled()) {
            $attributeCode = $this->helperData->getBrandCode();
            if ($attributeCode) {
                return $this->getAttributeValueByCode($product, $attributeCode);
            }
        }
        return null;
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getManufacturerValue($product)
    {
        if ($this->helperData->isManufacturerEnabled()) {
            $attributeCode = $this->helperData->getManufacturerCode();
            if ($attributeCode) {
                return $this->getAttributeValueByCode($product, $attributeCode);
            }
        }
        return null;
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getModelValue($product)
    {
        if ($this->helperData->isModelEnabled()) {
            $attributeCode = $this->helperData->getModelCode();
            if ($attributeCode) {
                return $this->getAttributeValueByCode($product, $attributeCode);
            }
        }
        return null;
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getGtinData($product)
    {
        if ($this->helperData->isGtinEnabled()) {
            $attributeCode = $this->helperData->getGtinCode();
            if (!$attributeCode) {
                return null;
            }

            $gtinValue = $this->getAttributeValueByCode($product, $attributeCode);
            if (preg_match('/^[0-9]+$/', $gtinValue)) {
                if (strlen($gtinValue) == 8) {
                    $gtinType = 'gtin8';
                } elseif (strlen($gtinValue) == 12) {
                    $gtinValue = '0' . $gtinValue;
                    $gtinType = 'gtin13';
                } elseif (strlen($gtinValue) == 13) {
                    $gtinType = 'gtin13';
                } elseif (strlen($gtinValue) == 14) {
                    $gtinType = 'gtin14';
                }
            }
        }

        return !empty($gtinType) ? ['gtinType' => $gtinType, 'gtinValue' => $gtinValue] : null;
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getSkuValue($product)
    {
        if ($this->helperData->isSkuEnabled()) {
            $attributeCode = $this->helperData->getSkuCode();
            if ($attributeCode) {
                $sku = $this->getAttributeValueByCode($product, $attributeCode);
            } else {
                $sku = $product->getSku();
            }
            return $sku;
        }
        return null;
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getWeightValue($product)
    {
        if ($this->helperData->isWeightEnabled()) {
            $weightValue = $product->getWeight();

            if ($weightValue) {
                $weightUnit  = $this->helperData->getWeightUnit();
                return $weightValue . ' ' . $weightUnit;
            }
        }
        return null;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return null|string
     */
    public function getPriceValidUntilValue($product)
    {
        if ($this->helperData->isUseSpecialPriceFunctionality()) {
            $fromDate = $product->getSpecialFromDate();
            $toDate   = $product->getSpecialToDate();

            $storeTimeStamp = $this->timezone->scopeTimeStamp($product->getStore());
            $fromTimeStamp  = strtotime($fromDate);
            $toTimeStamp    = strtotime($toDate);

            if ($toDate) {
                // fix date YYYY-MM-DD 00:00:00 to YYYY-MM-DD 23:59:59
                $toTimeStamp += 86399;
            }

            if (!$this->dateTime->isEmptyDate($fromDate) && $storeTimeStamp < $fromTimeStamp) {
                return date(DateTime::DATE_PHP_FORMAT, $fromTimeStamp);
            } elseif (!$this->dateTime->isEmptyDate($toDate) && $storeTimeStamp < $toTimeStamp) {
                return date(DateTime::DATE_PHP_FORMAT, $toTimeStamp);
            }
        }

        $value = $this->helperData->getPriceValidUntilDefaultValue();

        if ($value && strtotime($value)) {
            $value = date(DateTime::DATE_PHP_FORMAT, strtotime($value, 0));

            return $this->dateTime->isEmptyDate($value) ? null : $value;
        }

        return null;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return null|string
     */
    public function getProductIdValue($product)
    {
        $attributeCode = $this->helperData->getProductIdCode();

        if ($attributeCode) {
            $attributeValue = $this->getAttributeValueByCode($product, $attributeCode);

            return is_array($attributeValue) ? null : $attributeValue;
        }

        return null;
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $propertyName
     * @return string
     */
    public function getCustomPropertyValue($product, $propertyName)
    {
        $customProperty = $this->getAttributeValueByCode($product, $propertyName);
        return $customProperty ? $customProperty : null;
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getCategoryValue($product)
    {
        if (!$this->helperData->isCategoryEnabled()) {
            return null;
        }

        if (!is_null($this->categoryName)) {
            return $this->categoryName;
        }

        $categories = $product->getCategoryCollection()->exportToArray();
        $currentCategory = $this->registry->registry('current_category');
        $useDeepestCategory = $this->helperData->isCategoryDeepest();

        if (is_object($currentCategory)) {
            if (!count($categories)) {
                $this->categoryName = $currentCategory->getName();
                return $this->categoryName;
            }

            if ($useDeepestCategory) {
                $currentId = $currentCategory->getId();
                $currentLevel = $currentCategory->getLevel();
                if (!is_numeric($currentLevel)) {
                    $this->categoryName = $currentCategory->getName();
                    return $this->categoryName;
                }

                foreach ($categories as $category) {
                    if ($category['level'] > $currentLevel) {
                        $currentId = $category['entity_id'];
                        $currentLevel = $category['level'];
                    }
                }
                if ($currentId != $currentCategory->getId()) {
                    $categoryName = $this->getCategoryNameById($currentId);
                }
            }
            if (empty($categoryName)) {
                $this->categoryName = $currentCategory->getName();
            }
        } else {
            if (!$useDeepestCategory || !count($categories)) {
                $this->categoryName = '';
                return $this->categoryName;
            }

            $currentId = 0;
            $currentLevel = 0;
            if (is_numeric($currentLevel)) {
                foreach ($categories as $category) {
                    if ($category['level'] >= $currentLevel) {
                        $currentId = $category['entity_id'];
                        $currentLevel = $category['level'];
                    }
                }
                if ($currentId) {
                    $this->categoryName = $this->getCategoryNameById($currentId);
                }
            }
        }

        return $this->categoryName;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getProductImage($product, $imageId = 'product_base_image')
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes([])
            ->create();
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return boolean
     */
    public function getAvailability($product)
    {
        return $product->isAvailable();
    }

    /**
     *
     * @param int $id
     * @return string
     */
    protected function getCategoryNameById($id)
    {
        if ($id) {
            $storeId = $this->storeManager->getStore()->getId();

            return $this->resourceCategory->getAttributeRawValue(
                $id,
                'name',
                $this->storeManager->getStore($storeId)
            );
        }
        return '';
    }
}
