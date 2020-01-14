<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use MageWorx\SeoUrls\Model\Source\PagerMask;
use MageWorx\SeoAll\Helper\Layer as SeoAllHelperLayer;


class UrlBuildWrapper extends \Magento\Framework\Url\Helper\Data
{
    /**
     * @var \MageWorx\SeoUrls\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\SeoUrls\Helper\Url
     */
    protected $helperUrl;

    /**
     * @var \MageWorx\SeoUrls\Helper\Layer
     */
    protected $helperLayer;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $categoryHelper;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \MageWorx\SeoUrls\Helper\UrlBuilder\Layer
     */
    protected $seoLayerUrlBuilder;

    /**
     * @var SeoAllHelperLayer
     */
    protected $helperLayerAll;

    /**
     * UrlBuildWrapper constructor.
     * @param Data $helperData
     * @param Url $helperUrl
     * @param Layer $helperLayer
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param UrlBuilder\Layer $seoLayerUrlBuilder
     * @param SeoAllHelperLayer $helperLayerAll
     * @param Context $context
     */
    public function __construct(
        \MageWorx\SeoUrls\Helper\Data $helperData,
        \MageWorx\SeoUrls\Helper\Url $helperUrl,
        \MageWorx\SeoUrls\Helper\Layer $helperLayer,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \MageWorx\SeoUrls\Helper\UrlBuilder\Layer $seoLayerUrlBuilder,
        SeoAllHelperLayer $helperLayerAll,
        Context $context
    ) {
        $this->helperData         = $helperData;
        $this->helperUrl          = $helperUrl;
        $this->helperLayer        = $helperLayer;
        $this->categoryHelper     = $categoryHelper;
        $this->categoryRepository = $categoryRepository;
        $this->seoLayerUrlBuilder = $seoLayerUrlBuilder;
        $this->helperLayerAll     = $helperLayerAll;

        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getCurrentFiltersUrl()
    {
        $filterState = [];

        foreach ($this->getActiveFilters() as $item) {
            $filterState[$item->getFilter()->getRequestVar()] = $this->getAttributeValue($item);
        }

        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_escape'] = true;
        $params['_query'] = $filterState;

        return $this->seoLayerUrlBuilder->getLayerFilterUrl($params);
    }

    /**
     * Retrieve active filters
     *
     * @return array
     */
    public function getActiveFilters()
    {
        $filters = $this->helperLayerAll->getCurrentLayeredFilters();
        if (!is_array($filters)) {
            $filters = [];
        }
        return $filters;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $filterItem
     * @return string
     */
    public function getFilterUrl($filterItem)
    {
        if ($filterItem->getFilter() instanceof \Magento\CatalogSearch\Model\Layer\Filter\Category
            || $filterItem->getFilter() instanceof \Magento\Catalog\Model\Layer\Filter\Category
        ) {
            $url = $this->getCategoryFilterUrl($filterItem);
        } else {
            $url = $this->getAttributeFilterUrl($filterItem);
        }
        return $url;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $filterItem
     * @return string
     */
    public function getAttributeFilterUrl($filterItem)
    {
        $varName = $filterItem->getFilter()->getRequestVar();
        $value   = $this->getAttributeValue($filterItem);

        $query = [
            $varName => $value,
            $this->helperData->getPagerVariableName() => null // exclude current page from urls
        ];

        $url = $this->seoLayerUrlBuilder->getLayerFilterUrl(
            [
                '_current'     => true,
                '_use_rewrite' => true,
                '_query'       => $query
            ]
        );

        return $url;
    }

    /**
     * Retrieve attribute value (depends by attribute type)
     *
     * @param \Magento\Catalog\Model\Layer\Filter\Item $filterItem
     * @return mixed
     */
    public function getAttributeValue($filterItem)
    {
        $labelValues = [];

        if (method_exists($filterItem->getFilter(), 'getAttributeValues')) {
            $values = $filterItem->getFilter()->getAttributeValues();

            if ($values) {
                foreach ($values as $optionId) {
                    $labelValues[] = $filterItem->getFilter()->getAttributeModel()->getFrontend()->getOption($optionId);
                }
            }
        }

        $labelValues[] = $filterItem->getLabel();

        $attribute  = $filterItem->getFilter()->getData('attribute_model'); //->getAttributeCode()
        if ($attribute) {
            if ($attribute->getAttributeCode() == 'price' || $attribute->getBackendType() == 'decimal') {
                return $filterItem->getValue();
            }
        }

        $labelValues = array_unique($labelValues);
        return implode($this->helperLayerAll->getMultipleValueSeparator(), $labelValues);
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $filterItem
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryFilterUrl($filterItem)
    {
        $category    = $this->categoryRepository->get((int)$filterItem->getValue());
        $categoryUrl =  $this->categoryHelper->getCategoryUrl($category);
        $suffix      = $this->helperData->getSuffix();

        if ($suffix == "/") {
            $suffix = '';
        }
        if ($suffix && strpos($suffix, '.') === false) {
            $suffix = '.' . $suffix;
        }

        $categoryPart = $this->helperUrl->removeSuffix($categoryUrl, $suffix);
        $layeredPart  = $this->getLayeredPartFromUrl($filterItem);

        $categoryPart = str_replace('?___SID=U', '', $categoryPart);

        $url = $categoryPart . $layeredPart . $suffix;

        return $url;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $filterItem
     * @return string
     */
    public function getLayeredPartFromUrl($filterItem)
    {
        return '';
    }
}