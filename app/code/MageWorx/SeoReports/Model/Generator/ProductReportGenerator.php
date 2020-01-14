<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model\Generator;

use MageWorx\SeoReports\Api\Data\ProductReportInterface;
use MageWorx\SeoReports\Model\Config\Product;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;

class ProductReportGenerator extends \MageWorx\SeoReports\Model\Generator\AbstractReportGenerator
{
    /**
     * @var \MageWorx\SeoReports\Model\ResourceModel\ProductReport\CollectionFactory
     */
    protected $reportCollectionFactory;

    /**
     * @var \MageWorx\SeoReports\Model\ResourceModel\ProductReport\Collection
     */
    protected $reportCollection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $productStatus;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \MageWorx\SeoReports\Model\Config\Product
     */
    protected $reportConfig;

    /**
     * @var \MageWorx\SeoReports\Model\ReportDataConverter
     */
    protected $converter;

    /**
     * @var \MageWorx\SeoAll\Helper\Config
     */
    protected $helperConfig;

    /**
     * {@inheritDoc}
     */
    protected $reportTable = 'mageworx_seoreports_product';

    /**
     * @var array
     */
    protected $productIds = [];


    /**
     * ProductReportGenerator constructor.
     *
     * @param \MageWorx\SeoReports\Model\DataProvider $dataProvider
     * @param Product $reportConfig
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \MageWorx\SeoReports\Model\ReportDataConverter $dataConverter
     * @param \MageWorx\SeoReports\Model\ResourceModel\ProductReport\CollectionFactory $reportCollectionFactory
     * @param \MageWorx\SeoReports\Model\ResourceModel\ProductReport $reportResource
     * @param ProductReportInterface $productReport
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\SeoAll\Helper\Config $helperConfig
     */
    public function __construct(
        \MageWorx\SeoReports\Model\DataProvider $dataProvider,
        \MageWorx\SeoReports\Model\Config\Product $reportConfig,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \MageWorx\SeoReports\Model\ReportDataConverter $dataConverter,
        \MageWorx\SeoReports\Model\ResourceModel\ProductReport\CollectionFactory $reportCollectionFactory,
        \MageWorx\SeoReports\Model\ResourceModel\ProductReport $reportResource,
        \MageWorx\SeoReports\Api\Data\ProductReportInterface $productReport,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\SeoAll\Helper\Config $helperConfig
    ) {
        $this->product                 = $product;
        $this->collectionFactory       = $collectionFactory;
        $this->productStatus           = $productStatus;
        $this->productVisibility       = $productVisibility;
        $this->reportCollectionFactory = $reportCollectionFactory;
        $this->converter               = $dataConverter;
        $this->helperConfig            = $helperConfig;

        parent::__construct(
            $dataProvider,
            $reportConfig,
            $reportResource,
            $resource,
            $storeManager,
            $productReport,
            '\Magento\Catalog\Model\Product'
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

            $productCollection = $this->getProductCollection($store);
            $productCollection->setPageSize(100);
            $pageNum = $productCollection->getLastPageNumber();

            $this->joinUrlRewrite($productCollection);

            for ($currentPageNum = 1; $currentPageNum <= $pageNum; $currentPageNum++) {
                $productCollection->setCurPage($currentPageNum);

                $preparedData = [];

                foreach ($productCollection as $product) {
                    $product->setStoreId($store->getStoreId());
                    $preparedData[] = $this->getPreparedDataFullFormat($product);
                }

                $productCollection->clear();

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
     * @param int $storeId
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getProductCollection($storeId)
    {
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
        $collection->addAttributeToSelect($this->getFieldList());
        $collection->setStoreId($storeId);

        return $collection;
    }

    /**
     * @return string
     */
    protected function getReportTableName()
    {
        return $this->reportCollectionFactory->create()->getMainTable();
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function joinUrlRewrite($collection)
    {
        if (!$this->helperConfig->useCategoriesPathInProductUrl($collection->getStoreId())) {
            return $this->joinLongUrlRewrite($collection);
        }

        return $this->joinProductUrlRewrite($collection);
    }

    /**
     * Joins the root product's url rewrites to collection
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function joinProductUrlRewrite($collection)
    {
        $collection->joinTable(
            'url_rewrite',
            'entity_id = entity_id',
            ['request_path', 'target_path'],
            '{{table}}.entity_type = \'' . ProductUrlRewriteGenerator::ENTITY_TYPE . '\' AND {{table}}.store_id = ' . $collection->getStoreId(
            ) . ' AND {{table}}.target_path NOT LIKE \'%category%\' AND is_autogenerated = 1',
            'left'
        );

        return $collection;
    }

    /**
     * Joins the product's longest url rewrites to collection
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function joinLongUrlRewrite($collection)
    {
        $type    = ProductUrlRewriteGenerator::ENTITY_TYPE;
        $storeId = $collection->getStoreId();

        $maxLengthRequestPathSubQuery = $collection
            ->getConnection()
            ->select()
            ->from(
                ['url_b' => $this->resource->getTableName('url_rewrite')],
                [new \Zend_Db_Expr('MAX(LENGTH(`url_b`.`request_path`))')]
            )
            ->where('url_b.entity_type = ?', $type)
            ->where('url_b.store_id = ?', $storeId)
            ->where('url_b.entity_id = url_a.entity_id')
            ->where('url_b.is_autogenerated = 1');

        $collection
            ->getSelect()
            ->joinLeft(
                ['url_a' => $this->resource->getTableName('url_rewrite')],
                'e.entity_id = url_a.entity_id',
                ['url_a.request_path']
            )
            ->where('url_a.entity_type = ?', $type)
            ->where('url_a.store_id = ?', $storeId)
            ->where('url_a.is_autogenerated = 1')
            ->where(new \Zend_Db_Expr('LENGTH(`url_a`.`request_path`) = ?'), $maxLengthRequestPathSubQuery)
            ->group('url_a.entity_id');

        return $collection;
    }

    /**
     * @param int $entityId
     * @param int $storeId
     * @return \MageWorx\SeoReports\Model\ProductReport
     */
    protected function getReportByReferenceAndStoreId($entityId, $storeId)
    {
        $reportCollection = $this->reportCollectionFactory->create();
        $reportCollection->addReferenceIdToFilter($entityId);
        $reportCollection->addFieldToFilter('store_id', $storeId);

        /** @var \MageWorx\SeoReports\Model\ProductReport $report */
        return $reportCollection->getFirstItem();
    }


    /**
     * @param int $entityId
     * @return \MageWorx\SeoReports\Model\ResourceModel\ProductReport\Collection
     */
    protected function getReportCollectionByReference($entityId)
    {
        /** @var \MageWorx\SeoReports\Model\ResourceModel\ProductReport\Collection $reportCollection */
        $reportCollection = $this->reportCollectionFactory->create();
        $reportCollection->addReferenceIdToFilter($entityId);

        return $reportCollection;
    }
}