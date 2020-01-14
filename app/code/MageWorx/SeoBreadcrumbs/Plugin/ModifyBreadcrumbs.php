<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBreadcrumbs\Plugin;

use Magento\Framework\App\RequestInterface;
use MageWorx\SeoBreadcrumbs\Model\Source\Type;

class ModifyBreadcrumbs
{
    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     *
     * @var \MageWorx\SeoBreadcrumbs\Helper\Data
     */
    protected $helperData;

    /**
     * Breadcrumb Path cache
     *
     * @var string
     */
    protected $categoryPath;

    /**
     * Category collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * Product categories with parents after filters
     *
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $filteredCategories;

    /**
     * @var \MageWorx\SeoBreadcrumbs\Helper\Category
     */
    protected $helperCategory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     *
     * @param RequestInterface $request
     * @param \MageWorx\SeoBreadcrumbs\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \MageWorx\SeoBreadcrumbs\Helper\Data $helperData,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \MageWorx\SeoBreadcrumbs\Helper\Category $helperCategory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->request = $request;
        $this->helperData = $helperData;
        $this->coreRegistry = $coreRegistry;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->helperCategory = $helperCategory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Catalog\Helper\Data $helperCatalog
     * @param \Closure $proceed
     * @return array|string
     */
    public function aroundGetBreadcrumbPath(\Magento\Catalog\Helper\Data $helperCatalog, \Closure $proceed)
    {
        if (!$this->helperData->isSeoBreadcrumbsEnabled()) {
            return $proceed();
        }

        if ($this->helperData->getSeoBreadcrumbsType() == Type::BREADCRUMBS_TYPE_DEFAULT
            && !$this->helperData->isUseCategoryBreadcrumbsPriority()
        ) {
            return $proceed();
        }

        if ($this->request->getFullActionName() != 'catalog_product_view') {
            return $proceed();
        }

        $product = $helperCatalog->getProduct();

        if (!$product) {
            return $proceed();
        }

        if ($product->getId() != $this->request->getParam('id')) {
            return $proceed();
        }

        if ($this->categoryPath) {
            return $this->categoryPath;
        }

        /**
         * @var \Magento\Catalog\Model\Category $targetCategory
         */
        $targetCategory   = $this->getTargetCategory($product);

        if (!$targetCategory) {
            return $proceed();
        }

        return $this->getNewBreadcrumbPath($product, $targetCategory);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return null|\Magento\Catalog\Model\Category
     */
    protected function getTargetCategory($product)
    {
        /**
         * @see \MageWorx\SeoBreadcrumbs\Plugin\ExtendCategoryCollection
         * @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection
         */
        $collection = $product->getCategoryCollection()->setOrder(
            'level',
            \Magento\Framework\Data\Collection::SORT_ORDER_ASC
        );

        /**
         * @var \Magento\Catalog\Model\Category $targetCategory
         */
        $targetCategory = null;

        $loadedProductCategories      = [];
        $disabledProductCategoryIds   = [];

        $parentCategoryIds = [];
        $productCategories = $collection->getItems();

        foreach ($collection->getItems() as $category) {
            if (!$category->getIsActive()) {
                $disabledProductCategoryIds[] = $category->getId();
                continue;
            }

            if (in_array($category->getId(), $disabledProductCategoryIds)) {
                $disabledProductCategoryIds[] = $category->getId();
                continue;
            }

            if ($this->helperData->isShortMode()) {
                $parentId = $category->getParentId();
                if (!in_array($parentId, $this->helperCategory->getRootAndDefaultIds())
                    && !empty($productCategories[$parentId])
                ) {
                    continue;
                }
            }

            $loadedProductCategories[$category->getId()] = $category;
            $ids = explode(',', $category->getPathInStore());
            $parentCategoryIds = array_merge($parentCategoryIds, $ids);
        }

        if (!$loadedProductCategories) {
            return null;
        }

        $parentCategoryIds = array_filter($parentCategoryIds);
        $parentCategoryIds = array_unique($parentCategoryIds);

        $loadedProductCategoryIds = array_keys($loadedProductCategories);
        $parentCategoryIdsForLoad = array_diff($parentCategoryIds, $loadedProductCategoryIds);
        $loadedParentCategories   = $this->getCategoryCollection($parentCategoryIdsForLoad)->getItems();

        $allIds = array_merge($loadedProductCategoryIds, array_keys($loadedParentCategories));

        $targetCategory = $this->chooseTargetBySettings($loadedProductCategories, $allIds);
        $this->filteredCategories = $loadedProductCategories + $loadedParentCategories;

        return $targetCategory;
    }

    /**
     * @param array $loadedProductCategories
     * @param array $allIds
     * @param string $by
     * @return null|\Magento\Catalog\Model\Category
     */
    protected function chooseTargetBySettings($loadedProductCategories, $allIds)
    {
        $targetCategory      = null;
        $level               = null;
        $breadcrumbsPriority = null;

        foreach ($loadedProductCategories as $category) {
            $categoryParentIds = explode(',', $category->getPathInStore());

            if (count($categoryParentIds) != count(array_intersect($categoryParentIds, $allIds))) {
                continue;
            }

            if (in_array($category->getId(), $this->helperCategory->getRootAndDefaultIds())) {
                continue;
            }

            if ($level === null) {
                $level = $category->getLevel();
                $breadcrumbsPriority = $category->getBreadcrumbsPriority();
                $targetCategory = $category;
            } else {
                if ($this->helperData->isUseCategoryBreadcrumbsPriority()) {
                    if (!$this->isApproveByBreadcrumbsPriority($category, $breadcrumbsPriority, $level, $targetCategory->getId())) {
                        continue;
                    }
                } else {
                    if (!$this->isApproveByDepth($category, $level, $targetCategory->getId())) {
                        continue;
                    }
                }

                $targetCategory = $category;
                $level = (int)$category->getLevel();
                $breadcrumbsPriority = (int)$category->getBreadcrumbsPriority();
            }
        }

        return $targetCategory;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param int $breadcrumbsPriority
     * @param int $level
     * @param int $currentId
     * @return bool
     */
    protected function isApproveByBreadcrumbsPriority($category, $breadcrumbsPriority, $level, $currentId)
    {
        if ((int)$category->getBreadcrumbsPriority() > $breadcrumbsPriority) {
            return true;
        } elseif ((int)$category->getBreadcrumbsPriority() == $breadcrumbsPriority) {
            return $this->isApproveByDepth($category, $level, $currentId);
        }

        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param int $level
     * @param int $currentId
     * @return bool
     */
    protected function isApproveByDepth($category, $level, $currentId)
    {
        if ($this->helperData->isLongMode() && (int)$category->getLevel() > $level) {
            return true;
        } elseif ($this->helperData->isShortMode() &&  (int)$category->getLevel() < $level) {
            return true;
        } elseif (!$this->helperData->isDefaultMode() && (int)$category->getLevel() == $level) {
            if ($category->getId() < $currentId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Category $category
     * @return array|string
     */
    protected function getNewBreadcrumbPath($product, $category)
    {
        $path = [];
        $pathInStore = $category->getPathInStore();
        $pathIds = array_reverse(explode(',', $pathInStore));

        $categories = $this->filteredCategories;

        foreach ($pathIds as $categoryId) {
            if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                $path['category' . $categoryId] = [
                    'label' => $categories[$categoryId]->getName(),
                    'link' => $categories[$categoryId]->getUrl()
                ];
            }
        }

        $path['product'] = ['label' => $product->getName()];

        $this->categoryPath = $path;
        return $this->categoryPath;
    }

    /**
     * @param array $categoryIds
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    protected function getCategoryCollection($categoryIds)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->categoryCollectionFactory->create();
        $collection->setStore(
            $this->storeManager->getStore()->getId()
        )
        ->addNameToResult()
        ->addAttributeToSelect(
            'url_key'
        )
        ->addIdFilter(
            $categoryIds
        )
        ->addIsActiveFilter();

        if ($this->helperData->isUseCategoryBreadcrumbsPriority()) {
            $collection->addAttributeToSelect(
                \MageWorx\SeoBreadcrumbs\Helper\Data::BREADCRUMBS_PRIORITY_CODE,
                'left'
            );
        }

        return $collection;
    }
}
