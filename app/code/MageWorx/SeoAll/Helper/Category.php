<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Helper;

use Magento\Framework\App\Helper\Context;

class Category extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category
     */
    protected $resourceCategory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Category constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category $resourceCategory
     * @param Context $context
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \MageWorx\SeoAll\Model\ResourceModel\Category $resourceCategory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Context $context
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->resourceCategory = $resourceCategory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }


    /**
     * @param $id
     * @param null|int $storeId
     * @return array|bool|string
     */
    protected function getCategoryNameById($id, $storeId = null)
    {
        if ($id) {
            if ($storeId === null) {
                $storeId = $this->storeManager->getStore()->getId();
            }

            return $this->resourceCategory->getAttributeRawValue(
                $id,
                'name',
                $this->storeManager->getStore($storeId)
            );
        }
        return '';
    }

    /**
     * @param array $ids
     * @param null|int $storeId
     * @return array
     */
    public function getCategoryNames(array $ids, $storeId = null)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->categoryCollectionFactory->create();
        $collection->addIdFilter($ids);
        $collection->addAttributeToSelect('name');

        if ($storeId !== null) {
            $collection->setStoreId($storeId);
        }

        $result = [];
        foreach ($collection as $item) {
            $result[$item->getId()] = $item->getData('name');
        }
        return $result;
    }

    /**
     * @param int $id
     * @param null|int $storeId
     * @param bool $withRoot
     * @return array|mixed
     */
    public function getParentCategoryNamesById($id, $storeId, $withRoot = false)
    {
        $path = $this->getCategoryPathById($id, $storeId);

        if (!$path) {
            return [];
        }

        $rawIds = explode('/', $path);
        $ids = [];

        foreach ($rawIds as $id) {
            if ($id == 1) {
                continue;
            }
            if (!$withRoot && $id == $this->storeManager->getStore($storeId)->getRootCategoryId()) {
                continue;
            }
            $ids[] = $id;
        }

        array_filter($ids);

        if (!$ids) {
            return [];
        }

        return $this->getCategoryNames($ids, $storeId);
    }

    /**
     * @param $id
     * @param null|int $storeId
     * @return array|bool|string
     */
    protected function getCategoryPathById($id, $storeId = null)
    {
        if ($id) {
            if ($storeId === null) {
                $storeId = $this->storeManager->getStore()->getId();
            }

            $result = $this->resourceCategory->getAttributeRawValue(
                $id,
                'path',
                $this->storeManager->getStore($storeId)
            );

            if (!empty($result['path'])) {
                return $result['path'];
            }
        }
        return '';
    }
}
