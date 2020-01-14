<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Helper;

use \MageWorx\SeoExtended\Helper\Data as HelperData;
use \MageWorx\SeoAll\Helper\Layer as HelperLayer;
use \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\CollectionFactory as CategoryFilterCollectionFactory;

class SeoFilterProvider extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperLayer
     */
    protected $helperLayer;

    /**
     * @var \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\CollectionFactory
     */
    protected $categoryFilterCollectionFactory;

    /**
     * SeoFilterProvider constructor.
     * @param Data $helperData
     * @param HelperLayer $helperLayer
     * @param CategoryFilterCollectionFactory $categoryFilterCollectionFactory
     */
    public function __construct(
        HelperData $helperData,
        HelperLayer $helperLayer,
        CategoryFilterCollectionFactory $categoryFilterCollectionFactory
    ) {
        $this->helperData  = $helperData;
        $this->helperLayer = $helperLayer;
        $this->categoryFilterCollectionFactory = $categoryFilterCollectionFactory;
    }

    /**
     * @var MageWorx_SeoExtended_Model_Catalog_Category $seoFilter
     */
    protected $seoFilter;

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param int $storeId
     * @return bool|\Magento\Framework\DataObject|MageWorx_SeoExtended_Model_Catalog_Category
     */
    public function getSeoFilter($category, $storeId)
    {
        if ($this->seoFilter === null) {
            if (!$this->helperData->isUseSeoForCategoryFilters()) {
                $this->seoFilter = false;
                return $this->seoFilter;
            }

            $currentFiltersData = $this->helperLayer->getLayeredNavigationFiltersData();

            if (empty($currentFiltersData)) {
                $this->seoFilter = false;
                return $this->seoFilter;
            }

            if (count($currentFiltersData) > 1 && $this->helperData->isUseOnSingleFilterOnly()) {
                $this->seoFilter = false;
                return $this->seoFilter;
            }

            $attributeIds = array_keys($currentFiltersData);
            $attributeOptionIds = array_column($currentFiltersData,'value');
            $attributeOptionIds = array_filter($attributeOptionIds, 'is_numeric');
            $attributeOptionIds[] = '0'; //for all attribute values

            /** @var \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\Collection $collection */
            $collection = $this->categoryFilterCollectionFactory->create();

            $collection->getFilteredCollection($attributeIds, $category->getId(), $storeId, $attributeOptionIds);

            $this->seoFilter = $this->getFilterBySortOrder($currentFiltersData, $collection);
        }

        return $this->seoFilter;
    }

    /**
     * @param array $currentFiltersData
     * @return \MageWorx\SeoExtended\Model\CategoryFilter|false
     */
    protected function getFilterBySortOrder($currentFiltersData, $collection)
    {
        $hightPriorityFilter = false;
        $currentPosition    = false;

        foreach ($collection->getItems() as $filter) {

            if (!$hightPriorityFilter || (int)$currentFiltersData[$filter->getAttributeId()]['position'] < $currentPosition) {
                $hightPriorityFilter = $filter;
                $currentPosition = (int)$currentFiltersData[$filter->getAttributeId()]['position'];
            }
        }

        return $hightPriorityFilter;
    }
}
