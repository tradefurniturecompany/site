<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Plugin\LayerFilterItem;

use Magento\Framework\View\Element\Template;
use MageWorx\SeoAll\Helper\Layer as SeoAllHelperLayer;

class AfterGetRemoveUrl
{
    /**
     * @var \MageWorx\SeoUrls\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

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
     * AfterGetRemoveUrl constructor.
     * @param \MageWorx\SeoUrls\Helper\Data $helperData
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \MageWorx\SeoUrls\Helper\Layer $helperLayer
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \MageWorx\SeoUrls\Helper\UrlBuilder\Layer $seoLayerUrlBuilder
     * @param SeoAllHelperLayer $helperLayerAll
     */
    public function __construct(
        \MageWorx\SeoUrls\Helper\Data $helperData,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \MageWorx\SeoUrls\Helper\Layer $helperLayer,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \MageWorx\SeoUrls\Helper\UrlBuilder\Layer $seoLayerUrlBuilder,
        SeoAllHelperLayer $helperLayerAll
    ) {
        $this->helperData = $helperData;
        $this->urlHelper = $urlHelper;
        $this->helperLayer = $helperLayer;
        $this->categoryHelper = $categoryHelper;
        $this->categoryRepository = $categoryRepository;
        $this->seoLayerUrlBuilder = $seoLayerUrlBuilder;
        $this->helperLayerAll = $helperLayerAll;
    }

    public function afterGetRemoveUrl(\Magento\Catalog\Model\Layer\Filter\Item $filterItem, $url)
    {
        if ($this->out()) {
            return $url;
        }

        $labelValues = [];
        $multipleValueSeparator = $this->helperLayerAll->getMultipleValueSeparator();

        $valuesAsString = $filterItem->getFilter()->getResetOptionValue($filterItem->getValue());
        if (strpos($valuesAsString, $multipleValueSeparator) !== false) {
            $values = explode('-', $valuesAsString);
        } else {
            $values = [$valuesAsString];
        }

        foreach ($values as $optionId) {
            if ($filterItem->getFilter()->getData('attribute_model') !== null) {
                $labelValues[] = $filterItem->getFilter()->getAttributeModel()->getFrontend()->getOption($optionId);
            }
        }

        $requestVar = $filterItem->getFilter()->getRequestVar();
        $value = implode($multipleValueSeparator, $labelValues);

        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = [$requestVar => $value];
        $params['_escape'] = true;
        $url = $this->seoLayerUrlBuilder->getLayerFilterUrl($params);

        if (!$value) {
            $url = $this->urlHelper->removeRequestParam($url, $requestVar);
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
     * @return bool
     */
    protected function out()
    {
        if (!$this->helperData->getIsSeoFiltersEnable()) {
            return true;
        }

        return !$this->helperData->getIsCompatiblePage();
    }
}
