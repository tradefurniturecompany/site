<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect;

use \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

/**
 * Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $_idFieldName = 'redirect_id';

    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        //        \Magento\Framework\App\ResourceConnection $conn,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

//        $this->conn = $conn;
        $this->storeManager = $storeManager;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'MageWorx\SeoRedirects\Model\Redirect\CustomRedirect',
            'MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect'
        );
    }

    /**
     * Add filter by some text
     *
     * @todo
     * @param string $content
     * @return this
     */
    public function addContentFilter($content)
    {
        return $this->getSelect()->where("(?) LIKE CONCAT('%', TRIM(BOTH '+' FROM `keyword`), '%')", $content);
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string $columnName
     * @return void
     */
    protected function performAfterLoad($tableName, $columnName)
    {
        $items = $this->getColumnValues($columnName);

        if (count($items)) {
            foreach ($this as $item) {
                $entityId = $item->getData($columnName);

                $storeCode = $this->storeManager->getStore($this->getStoreId())->getCode();
                $item->setData('_first_store_id', $this->getStoreId());
                $item->setData('store_code', $storeCode);
                $item->setData('store_id', [$this->getStoreId()]);
            }
        }
    }

    /**
     * Add product filter
     *
     * @param $id
     * @return $this
     */
    public function addProductFilter($id)
    {
        $this->getSelect()->where('main_table.product_id = ?', $id);

        return $this;
    }

    /**
     * Add request path filter
     *
     * @param array|string $requestPaths
     * @return $this
     */
    public function addRequestPathsFilter($requestPaths)
    {
        if (!is_array($requestPaths)) {
            $requestPaths = [$requestPaths];
        }
        if (is_array($requestPaths)) {
            $this->getSelect()->where('main_table.request_path IN(?)', $requestPaths);
        }

        return $this;
    }

    /**
     * Add request entity filter
     *
     * @param int $catId
     * @param int $entityType
     * @return $this
     */
    public function addRequestEntityFilter($entityId, $entityType)
    {
        $this->addFieldToFilter(CustomRedirect::REQUEST_ENTITY_IDENTIFIER, $entityId);
        $this->addFieldToFilter(CustomRedirect::REQUEST_ENTITY_TYPE, $entityType);

        return $this;
    }

    /**
     * Add target entity filter
     *
     * @param int $entityId
     * @param int $entityType
     * @return $this
     */
    public function addTargetEntityFilter($entityId, $entityType)
    {
        $this->addFieldToFilter(CustomRedirect::TARGET_ENTITY_IDENTIFIER, $entityId);
        $this->addFieldToFilter(CustomRedirect::TARGET_ENTITY_TYPE, $entityType);

        return $this;
    }

    /**
     * Add request category filter
     *
     * @param int $catId
     * @return $this
     */
    public function addRequestCategoryFilter($catId)
    {
        $this->addFieldToFilter(CustomRedirect::REQUEST_ENTITY_IDENTIFIER, $catId);
        $this->addFieldToFilter(CustomRedirect::REQUEST_ENTITY_TYPE, CustomRedirect::REDIRECT_TYPE_CATEGORY);

        return $this;
    }

    /**
     * Add target category filter
     *
     * @param int $catId
     * @return $this
     */
    public function addTargetCategoryFilter($catId)
    {
        $this->addFieldToFilter(CustomRedirect::TARGET_ENTITY_IDENTIFIER, $catId);
        $this->addFieldToFilter(CustomRedirect::TARGET_ENTITY_TYPE, CustomRedirect::REDIRECT_TYPE_CATEGORY);

        return $this;
    }

    /**
     * Add request product filter
     *
     * @param int $catId
     * @return $this
     */
    public function addRequestProductFilter($productId)
    {
        $this->addFieldToFilter(CustomRedirect::REQUEST_ENTITY_IDENTIFIER, $productId);
        $this->addFieldToFilter(CustomRedirect::REQUEST_ENTITY_TYPE, CustomRedirect::REDIRECT_TYPE_PRODUCT);

        return $this;
    }

    /**
     * Add target product filter
     *
     * @param int $catId
     * @return $this
     */
    public function addTargetProductFilter($productId)
    {
        $this->addFieldToFilter(CustomRedirect::TARGET_ENTITY_IDENTIFIER, $productId);
        $this->addFieldToFilter(CustomRedirect::TARGET_ENTITY_TYPE, CustomRedirect::REDIRECT_TYPE_PRODUCT);

        return $this;
    }

    /**
     * Add request product filter
     *
     * @param int $catId
     * @return $this
     */
    public function addRequestPageFilter($pageId)
    {
        $this->addFieldToFilter(CustomRedirect::REQUEST_ENTITY_IDENTIFIER, $pageId);
        $this->addFieldToFilter(CustomRedirect::REQUEST_ENTITY_TYPE, CustomRedirect::REDIRECT_TYPE_PAGE);

        return $this;
    }

    /**
     * Add target product filter
     *
     * @param int $catId
     * @return $this
     */
    public function addTargetPageFilter($pageId)
    {
        $this->addFieldToFilter(CustomRedirect::TARGET_ENTITY_IDENTIFIER, $pageId);
        $this->addFieldToFilter(CustomRedirect::TARGET_ENTITY_TYPE, CustomRedirect::REDIRECT_TYPE_PAGE);

        return $this;
    }

    /**
     * Add date filter (all rows older than retrieved period in days) to collection
     *
     * @param int $dayNums
     * @return $this
     */
    public function addDateFilter($dayNums)
    {
        $dayNums = (int)$dayNums;
        $this->getSelect()->where(
            new \Zend_Db_Expr("DATE(`main_table`.`date_created`) < (CURDATE()- {$dayNums})")
        );

        return $this;
    }

    /**
     * Add enabled filter
     *
     * @return MageWorx_SeoRedirects_Model_Resource_Redirect_Product_Collection
     */
    public function addEnableStatusFilter()
    {
        $this->getSelect()->where(
            'main_table.status = ?',
            \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect::STATUS_ENABLED
        );

        return $this;
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }

        return $this;
    }

    /**
     * Perform adding filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return void
     */
    protected function performAddStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store_id', ['in' => $store], 'public');
    }

    public function addFrontendFilter($orConditions = [])
    {
        $orParts = [];

        foreach ($orConditions as $andConditions) {
            $andList = [];

            foreach ($andConditions as $key => $value) {
                $andList[] = $this->_conn->quoteInto("$key = ?", $value);
            }

            $orParts[] = "(" . implode($andList, ' AND ') . ')';
        }

        $query = implode(' OR ', $orParts);

        $this->getSelect()->where(new \Zend_Db_Expr(implode(' OR ', $orParts)));

        return $this;
    }
}
