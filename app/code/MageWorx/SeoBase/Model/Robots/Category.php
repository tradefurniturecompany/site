<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Robots;

use MageWorx\SeoBase\Helper\Data as HelperData;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use MageWorx\SeoAll\Helper\Layer as HelperLayer;

/**
 * SEO Base category robots model
 */
class Category extends \MageWorx\SeoBase\Model\Robots
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var HelperLayer
     */
    protected $helperLayer;

    /**
     * Category constructor.
     * @param HelperData $helperData
     * @param Registry $registry
     * @param RequestInterface $request
     * @param UrlInterface $url
     * @param HelperLayer $helperLayer
     * @param $fullActionName
     */
    public function __construct(
        HelperData $helperData,
        Registry $registry,
        RequestInterface $request,
        UrlInterface $url,
        HelperLayer $helperLayer,
        $fullActionName
    ) {
        $this->registry = $registry;
        $this->helperLayer = $helperLayer;
        parent::__construct($helperData, $request, $url, $fullActionName);
    }

    /**
     * Retrieve final robots
     *
     * @return string
     */
    public function getRobots()
    {
        $metaRobots = $this->getCategoryRobots();
        return $metaRobots ? $metaRobots : $this->getRobotsBySettings();
    }

    /**
     * Retrieve robots from category atttibute/by layered navigation condition
     *
     * @return string|null
     */
    protected function getCategoryRobots()
    {
        $category = $this->registry->registry('current_category');
        if (is_object($category)) {
            $maxFilters   = $this->helperData->getCountFiltersForNoindex();
            $countFilters = count($this->helperLayer->getCurrentLayeredFilters());

            if ($this->helperData->isUseNoindexIfFilterMultipleValues() && $this->helperLayer->isUsedMultipleSelectionInLayer()) {
                return 'NOINDEX, FOLLOW';
            }

            $filterCodesAsString = $this->getCurrentFilterCodesAsString();
            if ($filterCodesAsString) {
                $attributeSettings = $this->helperData->getAttributeRobotsSettings();
                if (!empty($attributeSettings[$filterCodesAsString])) {
                    return $attributeSettings[$filterCodesAsString];
                }
            }

            if ($countFilters && $maxFilters !== '' && !is_null($maxFilters) && $countFilters >= $maxFilters) {
                return 'NOINDEX, FOLLOW';
            }

            if ($category->getMetaRobots()) {
                return $category->getMetaRobots();
            }

            $robotsForLn = $this->helperData->getCategoryLnRobots();
            if ($countFilters && $robotsForLn) {
                 return $robotsForLn;
            }
        }
        return null;
    }

    /**
     * @return string
     */
    protected function getCurrentFilterCodesAsString()
    {
        $filterData = $this->helperLayer->getCurrentLayeredFilters();
        $codes = array();
        if (is_array($filterData) && count($filterData)) {
            foreach ($filterData as $filter) {
                if (!empty($filter->getFilter()->getRequestVar())) {
                    $codes[] = $filter->getFilter()->getRequestVar();
                }
            }
            sort($codes);
        }

        return implode('+', $codes);
    }
}
