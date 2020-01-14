<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Helper;

/**
 * Class Layer
 */
class Layer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\Layer\Category
     */
    protected $catalogLayer;

    /**
     * Layer constructor.
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\App\Helper\Context $context
    ) {
    
        $this->layerResolver = $layerResolver;
        parent::__construct($context);
    }

    public function getCatalogLayer()
    {
        if ($this->catalogLayer) {
            return $this->catalogLayer;
        }
        return $this->catalogLayer = $this->layerResolver->get();
    }

    /**
     * @return string
     */
    public function getMultipleValueSeparator()
    {
        return '_';
    }

    /**
     * @return array
     */
    public function getCurrentLayeredFilters()
    {
        $this->catalogLayer = $this->getCatalogLayer();

        if (is_object($this->catalogLayer)
            && is_object($this->catalogLayer->getState())
            && is_array($this->catalogLayer->getState()->getFilters())
        ) {
            return $this->catalogLayer->getState()->getFilters();
        }
        return [];
    }

    /**
     * @return boolean
     */
    public function isCategoryFilterActive()
    {
        $items = $this->getCurrentLayeredFilters();
        if ($items) {
            foreach ($items as $item) {
                if ($item->getFilter()->getRequestVar() == 'cat') {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isUsedMultipleSelectionInLayer()
    {
        $filtersData = $this->getLayeredNavigationFiltersData();

        if ($filtersData) {
            foreach ($filtersData as $data) {
                if (!empty($data['is_multiple_value'])) {
                    return $data['is_multiple_value'];
                }
            }
        }
        return false;
    }

    /**
     * Retrieve specific filters data as array (use for canonical url)
     * @return array|false
     */
    public function getLayeredNavigationFiltersData()
    {
        $filterData     = [];

        $this->catalogLayer = $this->getCatalogLayer();
        $appliedFilters = $this->catalogLayer->getState()->getFilters();

        if (is_array($appliedFilters) && count($appliedFilters) > 0) {

            /** @var \Magento\Catalog\Model\Layer\Filter\Item $item */
            foreach ($appliedFilters as $item) {
                if (!$item->getFilter()->getData('attribute_model')) {
                    $attributeId = null;
                    //Ex: If $item->getFilter()->getRequestVar() == 'cat'
                    $use_in_canonical = 0;
                    $position         = 0;
                } else {
                    $attributeModel = $item->getFilter()->getAttributeModel();
                    $attributeId    = $attributeModel->getAttributeId();

                    $use_in_canonical = $item->getFilter()->getAttributeModel()->getLayeredNavigationCanonical();
                    $position         = $item->getFilter()->getAttributeModel()->getPosition();
                }

                $isMultipleValue = false;

                if (method_exists($item->getFilter(), 'getAttributeValues')) {
                    if (count($item->getFilter()->getAttributeValues()) > 1) {
                        $isMultipleValue = true;
                    }
                }

                $filterData[$attributeId] = [
                    'attribute_id'     => $attributeId,
                    'name'             => $item->getName(),
                    'is_multiple_value'=> $isMultipleValue,
                    'label'            => $item->getLabel(),
                    'code'             => $item->getFilter()->getRequestVar(),
                    'value'            => $this->_getRequest()->getParam($item->getFilter()->getRequestVar()),
                    'use_in_canonical' => $use_in_canonical,
                    'position'         => $position
                ];
            }
        }

        return (count($filterData) > 0) ? $filterData : false;
    }

    /**
     * Retrieve list of current filter codes
     *
     * @return array
     */
    public function getLayeredNavigationFiltersCode()
    {
        $filterCodes    = [];

        $this->catalogLayer = $this->getCatalogLayer();
        $appliedFilters = $this->catalogLayer->getState()->getFilters();

        if (is_array($appliedFilters) && count($appliedFilters) > 0) {
            foreach ($appliedFilters as $item) {
                $filterCodes[] = $item->getFilter()->getRequestVar();
            }
        }
        return $filterCodes;
    }
}
