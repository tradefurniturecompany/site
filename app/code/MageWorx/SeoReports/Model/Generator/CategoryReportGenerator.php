<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model\Generator;

use MageWorx\SeoReports\Api\Data\CategoryReportInterface;
use MageWorx\SeoReports\Model\Config\Category;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;

class CategoryReportGenerator extends \MageWorx\SeoReports\Model\Generator\AbstractReportGenerator
{
    /**
     * @var \MageWorx\SeoReports\Model\ResourceModel\CategoryReport\CollectionFactory
     */
    protected $reportCollectionFactory;

    /**
     * @var \MageWorx\SeoReports\Model\ResourceModel\CategoryReport\Collection
     */
    protected $reportCollection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $category;

    /**
     * @var \MageWorx\SeoReports\Model\Config\Category
     */
    protected $reportConfig;

    /**
     * @var \MageWorx\SeoReports\Model\ReportDataConverter
     */
    protected $converter;

    /**
     * @var array
     */
    protected $categoryIds = [];

    /**
     * CategoryReportGenerator constructor.
     *
     * @param \MageWorx\SeoReports\Model\DataProvider $dataProvider
     * @param Category $reportConfig
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     * @param \MageWorx\SeoReports\Model\ReportDataConverter $dataConverter
     * @param \MageWorx\SeoReports\Model\ResourceModel\CategoryReport\CollectionFactory $reportCollectionFactory
     * @param \MageWorx\SeoReports\Model\ResourceModel\CategoryReport $reportResource
     * @param CategoryReportInterface $categoryReport
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \MageWorx\SeoReports\Model\DataProvider $dataProvider,
        \MageWorx\SeoReports\Model\Config\Category $reportConfig,
        \Magento\Catalog\Model\Category $category,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \MageWorx\SeoReports\Model\ReportDataConverter $dataConverter,
        \MageWorx\SeoReports\Model\ResourceModel\CategoryReport\CollectionFactory $reportCollectionFactory,
        \MageWorx\SeoReports\Model\ResourceModel\CategoryReport $reportResource,
        \MageWorx\SeoReports\Api\Data\CategoryReportInterface $categoryReport,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->category                = $category;
        $this->collectionFactory       = $collectionFactory;
        $this->reportCollectionFactory = $reportCollectionFactory;
        $this->converter               = $dataConverter;

        parent::__construct(
            $dataProvider,
            $reportConfig,
            $reportResource,
            $resource,
            $storeManager,
            $categoryReport,
            '\Magento\Catalog\Model\Category'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function fillEntitiesPreparedData()
    {
        $stores = $this->storeManager->getStores();

        $this->connection->beginTransaction();

        /** @var \Magento\Store\Model\Store $store */
        foreach ($stores as $store) {

            $this->categoryIds = [];

            $rootCategoryId = $store->getRootCategoryId();
            $this->addChild($rootCategoryId, $store->getId());

            foreach (array_chunk($this->categoryIds, 20) as $categoryIdsPart) {
                $categoryCollection = $this->getCategoryCollection($store->getCategoryId());
                $categoryCollection->setStoreId($store->getId());
                $categoryCollection->addIdFilter($categoryIdsPart);
                $this->joinUrlRewrite($categoryCollection);

                $preparedData = [];

                foreach ($categoryCollection as $category) {
                    $category->setStoreId($categoryCollection->getStoreId());
                    $preparedData[] = $this->getPreparedDataFullFormat($category);
                }

                if (!$preparedData) {
                    continue;
                }

                $this->connection->insertMultiple(
                    $this->resource->getTableName($this->getReportTableName()),
                    $preparedData
                );
            }
        }

        $this->connection->commit();
    }

    /**
     * Joins url rewrite rules to collection
     *
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection $collection
     *
     * @return $this
     */
    public function joinUrlRewrite($collection)
    {
        $collection->joinTable(
            'url_rewrite',
            'entity_id = entity_id',
            ['request_path'],
            sprintf(
                '{{table}}.is_autogenerated = 1 AND {{table}}.store_id = %d AND {{table}}.entity_type = \'%s\'',
                $collection->getStoreId(),
                CategoryUrlRewriteGenerator::ENTITY_TYPE
            ),
            'left'
        );

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    protected function getReportTableName()
    {
        return $this->reportCollectionFactory->create()->getMainTable();
    }

    /**
     * @param int $parentId
     * @param int $storeId
     * @return $this
     */
    protected function addChild($parentId, $storeId)
    {
        $collection = $this->category->setStoreId($storeId)->getCategories($parentId);

        foreach ($collection as $category) {

            if (isset($this->categoryIds[$category->getId()])) {
                continue;
            }

            $this->categoryIds[$category->getId()] = $category->getId();
            if ($category->getChildrenCount() > 0) {
                $this->addChild($category->getId(), $storeId);
            }
        }

        return $this;
    }

    /**
     * @param int $storeId
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCategoryCollection($storeId)
    {
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect($this->getFieldList());
        $collection->setStoreId($storeId)->addFieldToFilter('is_active', 1);

        return $collection;
    }

    /**
     * @param int $entityId
     * @param int $storeId
     * @return \MageWorx\SeoReports\Model\CategoryReport
     */
    protected function getReportByReferenceAndStoreId($entityId, $storeId)
    {
        $reportCollection = $this->reportCollectionFactory->create();
        $reportCollection->addReferenceIdToFilter($entityId);
        $reportCollection->addFieldToFilter('store_id', $storeId);

        /** @var \MageWorx\SeoReports\Model\CategoryReport $report */
        return $reportCollection->getFirstItem();
    }

    /**
     * @param int $entityId
     * @return \MageWorx\SeoReports\Model\ResourceModel\CategoryReport\Collection
     */
    protected function getReportCollectionByReference($entityId)
    {
        /** @var \MageWorx\SeoReports\Model\ResourceModel\CategoryReport\Collection $reportCollection */
        $reportCollection = $this->reportCollectionFactory->create();
        $reportCollection->addReferenceIdToFilter($entityId);

        return $reportCollection;
    }
}