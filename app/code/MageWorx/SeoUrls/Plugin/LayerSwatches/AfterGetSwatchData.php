<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Plugin\LayerSwatches;

use \MageWorx\SeoUrls\Model\Source\PagerMask;
use Magento\Framework\View\Element\Template;
use Magento\Swatches\Block\LayeredNavigation\RenderLayered;
use MageWorx\SeoAll\Helper\Layer as SeoAllHelperLayer;

class AfterGetSwatchData
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
     * AfterGetUrl constructor.
     * @param \MageWorx\SeoUrls\Helper\Data $helperData
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \MageWorx\SeoUrls\Helper\Layer $helperLayer
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \MageWorx\SeoUrls\Helper\UrlBuilder\Layer $seoLayerUrlBuilder
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
        $this->helperData         = $helperData;
        $this->urlHelper          = $urlHelper;
        $this->helperLayer        = $helperLayer;
        $this->categoryHelper     = $categoryHelper;
        $this->categoryRepository = $categoryRepository;
        $this->seoLayerUrlBuilder = $seoLayerUrlBuilder;
        $this->helperLayerAll     = $helperLayerAll;
    }

    /**
     * @param RenderLayered $filterItem
     * @param $data
     * @return mixed
     */
    public function afterGetSwatchData(RenderLayered $filterItem, $data)
    {
        if ($this->out()) {
            return $data;
        }

        if (!empty($data['options'])) {
            foreach ($data['options'] as $optionId => $optionData) {
                $data['options'][$optionId]['link'] = $this->getAttributeFilterUrl(
                    $filterItem,
                    $data,
                    $optionId
                );
            }
        }

        return $data;
    }

    /**
     * @param RenderLayered $block
     * @param string $attributeCode
     * @param string $value
     * @return string
     */
    public function getAttributeFilterUrl($block, $data, $optionId)
    {
        $attributeCode = $data['attribute_code'];
        $value = $data['options'][$optionId]['label'];
        $varName = $attributeCode;

        /** @var \Magento\CatalogSearch\Model\Layer\Filter\Attribute|\MageWorx\LayeredNavigation\Model\Catalog\Layer\Filter\Attribute $filter */
        $filter = $block->getFilter();
        $labelValues = [];

        if (method_exists($filter, 'getAttributeValues')) {
            $values = $filter->getAttributeValues();

            if ($values) {
                foreach ($values as $optionId) {
                    $labelValues[] = $filter->getAttributeModel()->getFrontend()->getOption($optionId);
                }
            }
        }

        $labelValues[] = $value;
        $labelValues = array_unique($labelValues);

        $value = implode($this->helperLayerAll->getMultipleValueSeparator(), $labelValues);

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
