<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Model\FiltersConvertor;

use MageWorx\SeoExtended\Helper\Data as HelperData;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;

/**
 * SEO Extended category filter convertor model
 */
class Category extends \MageWorx\SeoExtended\Model\FiltersConvertor
{
    /**
     * @var \Magento\Catalog\Model\Layer\Category
     */
    protected $catalogLayer;

    /**
     *
     * @param \MageWorx\SeoExtended\Helper\HelperData $helperData
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \MageWorx\SeoExtended\Model\FiltersConvertor\LayerResolver $layerResolver
     * @param string $fullActionName
     */
    public function __construct(
        HelperData $helperData,
        \Magento\Framework\App\RequestInterface $request,
        LayerResolver $layerResolver,
        $fullActionName = null
    ) {
        parent::__construct($helperData, $request, $fullActionName);
        $this->catalogLayer = $layerResolver->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getStringByFilters()
    {
        $appliedFilters = $this->catalogLayer->getState()->getFilters();

        if (empty($appliedFilters)) {
            return '';
        }

        $commonFilters = [];

        foreach ($appliedFilters as $item) {
//            For exclude category filter use isUseFilter() method
            $name = is_object($item->getName()) ? $item->getName()->__toString() : $item->getName();
            $commonFilters[$name][] = $item->getLabel();
        }

        $value = '';
        foreach ($commonFilters as $filterName => $filterLabels) {
            $value .= $filterName . ": " . strip_tags(implode(', ', $filterLabels)) .  '; ';
        }

        return rtrim($value, '; ');
    }

    /**
     * Exclude category filter
     *
     * @param \Magento\Catalog\Model\Layer\Filter\Item $item
     * @return boolean
     */
    protected function isUseFilter($item)
    {
        $attributeModel = $item->getFilter()->getData('attribute_model');
        if (!$attributeModel) {
            return false;
        }
        return true;
    }
}
