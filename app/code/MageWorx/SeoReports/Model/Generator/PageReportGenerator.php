<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model\Generator;

use Magento\CmsUrlRewrite\Model\CmsPageUrlRewriteGenerator;

class PageReportGenerator extends \MageWorx\SeoReports\Model\Generator\AbstractReportGenerator
{
    /**
     * @var \MageWorx\SeoReports\Model\ResourceModel\PageReport\CollectionFactory
     */
    protected $reportCollectionFactory;

    /**
     * @var \MageWorx\SeoReports\Model\ResourceModel\PageReport\Collection
     */
    protected $reportCollection;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $page;

    /**
     * @var \MageWorx\SeoReports\Model\ReportDataConverter
     */
    protected $converter;

    /**
     * {@inheritDoc}
     */
    protected $reportTable = 'mageworx_seoreports_page';

    /**
     * PageReportGenerator constructor.
     *
     * @param \MageWorx\SeoReports\Model\DataProvider $dataProvider
     * @param \MageWorx\SeoReports\Model\Config\Page $reportConfig
     * @param \Magento\Cms\Model\Page $page
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $collectionFactory
     * @param \MageWorx\SeoReports\Model\ReportDataConverter $dataConverter
     * @param \MageWorx\SeoReports\Model\ResourceModel\PageReport\CollectionFactory $reportCollectionFactory
     * @param \MageWorx\SeoReports\Model\ResourceModel\PageReport $reportResource
     * @param \MageWorx\SeoReports\Api\Data\PageReportInterface $pageReport
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \MageWorx\SeoReports\Model\DataProvider $dataProvider,
        \MageWorx\SeoReports\Model\Config\Page $reportConfig,
        \Magento\Cms\Model\Page $page,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $collectionFactory,
        \MageWorx\SeoReports\Model\ReportDataConverter $dataConverter,
        \MageWorx\SeoReports\Model\ResourceModel\PageReport\CollectionFactory $reportCollectionFactory,
        \MageWorx\SeoReports\Model\ResourceModel\PageReport $reportResource,
        \MageWorx\SeoReports\Api\Data\PageReportInterface $pageReport,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->page                    = $page;
        $this->collectionFactory       = $collectionFactory;
        $this->reportCollectionFactory = $reportCollectionFactory;
        $this->converter               = $dataConverter;

        parent::__construct(
            $dataProvider,
            $reportConfig,
            $reportResource,
            $resource,
            $storeManager,
            $pageReport,
            '\Magento\Cms\Model\Page'
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

            $collection = $this->getPageCollection($store);

            $preparedData = [];

            foreach ($collection as $page) {
                $page->setStoreId($store->getStoreId());
                $preparedData[] = $this->getPreparedDataFullFormat($page);
            }

            if (!$preparedData) {
                continue;
            }

            $this->connection->insertMultiple(
                $this->resource->getTableName($this->getReportTableName()),
                $preparedData
            );
        }

        $this->connection->commit();
    }

    /**
     * {@inheritdoc}
     */
    protected function getReportTableName()
    {
        return $this->reportCollectionFactory->create()->getMainTable();
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return \Magento\Cms\Model\ResourceModel\Page\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getPageCollection($store)
    {
        /** @var \Magento\Cms\Model\ResourceModel\Page\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addStoreFilter($store);
        $collection->addFieldToFilter('is_active', \Magento\Cms\Model\Page::STATUS_ENABLED);
        $this->joinUrlRewrite($collection, $store);

        return $collection;
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @param \Magento\Cms\Model\ResourceModel\Page\Collection $collection
     * @return \Magento\Cms\Model\ResourceModel\Page\Collection
     */
    protected function joinUrlRewrite($collection, $store)
    {
        $collection->getSelect()
                   ->joinLeft(
                       ['urls' => $this->resource->getTableName('url_rewrite')],
                       'page_id = entity_id',
                       ['request_path', 'target_path']
                   )
                   ->where("urls.entity_type = '" . CmsPageUrlRewriteGenerator::ENTITY_TYPE . "'")
                   ->where('urls.store_id = ?', (int)$store->getId())
                   ->where('is_autogenerated = 1');

        return $collection;
    }

    /**
     * @param int $entityId
     * @param int $storeId
     * @return \MageWorx\SeoReports\Model\PageReport
     */
    protected function getReportByReferenceAndStoreId($entityId, $storeId)
    {
        $reportCollection = $this->reportCollectionFactory->create();
        $reportCollection->addReferenceIdToFilter($entityId);
        $reportCollection->addFieldToFilter('store_id', $storeId);

        /** @var \MageWorx\SeoReports\Model\PageReport $report */
        return $reportCollection->getFirstItem();
    }

    /**
     * @param int $entityId
     * @return \MageWorx\SeoReports\Model\ResourceModel\PageReport\Collection
     */
    protected function getReportCollectionByReference($entityId)
    {
        /** @var \MageWorx\SeoReports\Model\ResourceModel\PageReport\Collection $reportCollection */
        $reportCollection = $this->reportCollectionFactory->create();
        $reportCollection->addReferenceIdToFilter($entityId);

        return $reportCollection;
    }
}