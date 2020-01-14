<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBreadcrumbs\Plugin;

class ExtendCategoryCollection
{
    /**
     *
     * @var \MageWorx\SeoBreadcrumbs\Helper\Data
     */
    protected $helperData;

    /**
     * Instance of category collection.
     *
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    protected $categoryCollection;

    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * ExtendCategoryCollection constructor.
     * @param \MageWorx\SeoBreadcrumbs\Helper\Data $helperData
     */
    public function __construct(
        \MageWorx\SeoBreadcrumbs\Helper\Data $helperData,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->helperData = $helperData;
        $this->request = $request;
    }


    /**
     * Add additional attributes to product category collection
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection $result
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function afterGetCategoryCollection($product, $result)
    {
        if (!$this->helperData->isSeoBreadcrumbsEnabled()) {
            return $result;
        }

        if ($this->request->getFullActionName() != 'catalog_product_view') {
            return $result;
        }

        if (!($result instanceof \Magento\Catalog\Model\ResourceModel\Category\Collection)) {
            return $result;
        }

        if (!is_object($product)) {
            return $result;
        }

        if ($product->getId() != $this->request->getParam('id')) {
            return $result;
        }

        if ($this->categoryCollection === null) {
            $result->addAttributeToSelect(['name', 'url_key', 'is_active']);
            if ($this->helperData->isUseCategoryBreadcrumbsPriority()) {
                $result->addAttributeToSelect(
                    \MageWorx\SeoBreadcrumbs\Helper\Data::BREADCRUMBS_PRIORITY_CODE,
                    'left'
                );
            }
            $this->categoryCollection = $result;
        }

        return $result;
    }
}
