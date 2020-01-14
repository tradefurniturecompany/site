<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\ResourceModel\Catalog\Product;

use MageWorx\SeoBase\Model\Source\CanonicalType;

class FlexibleCanonical extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \MageWorx\SeoBase\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $urlType;

    /**
     * @var array
     */
    protected $storeRootCategories = [];

    /**
     * FlexibleCanonical constructor.
     *
     * @param \MageWorx\SeoAll\Helper\Config $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param null $connectionName
     */
    public function __construct(
        \MageWorx\SeoBase\Helper\Data $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        $this->helperData   = $helperData;
        $this->storeManager = $storeManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_product_entity', 'entity_id');
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param int|array $storeIds In case is array - stores must have identical URL Types!
     * @param string $urlRewriteAlias
     */
    public function addFlexibleConditions($select, $storeIds, $urlRewriteAlias = 'url_rewrite')
    {
        if (is_array($storeIds)) {
            $storeId = $storeIds[0];
        } else {
            $storeId = $storeIds;
            $storeIds = [$storeIds];
        }

        $this->urlType = $this->helperData->getProductCanonicalUrlType($storeId);

        $orderFirstCondition = new \Zend_Db_Expr(
            "INSTR(`target_path`, '/category/') " . \Zend_Db_Select::SQL_DESC
        );

        switch ($this->urlType) {
            case CanonicalType::URL_TYPE_LONGEST:
                $orderSecondCondition = new \Zend_Db_Expr(
                    'LENGTH(`request_path`) ' . \Zend_Db_Select::SQL_DESC
                );
                break;
            case CanonicalType::URL_TYPE_SHORTEST:
                $orderSecondCondition = new \Zend_Db_Expr(
                    'LENGTH(`request_path`) ' . \Zend_Db_Select::SQL_ASC
                );
                break;
            case CanonicalType::URL_TYPE_MAX_CATEGORY_LEVEL:
                $orderSecondCondition = new \Zend_Db_Expr(
                    "CHAR_LENGTH(`request_path`) - CHAR_LENGTH(REPLACE(`request_path`,'/','')) " . \Zend_Db_Select::SQL_DESC
                );
                break;
            case CanonicalType::URL_TYPE_MIN_CATEGORY_LEVEL:
                $orderSecondCondition = new \Zend_Db_Expr(
                    "CHAR_LENGTH(`request_path`) - CHAR_LENGTH(REPLACE(`request_path`,'/','')) " . \Zend_Db_Select::SQL_ASC
                );
                break;
            case CanonicalType::URL_TYPE_NO_CATEGORIES:
                $orderFirstCondition  = null;
                $orderSecondCondition = null;
        }

        $this->addUrlRewriteWhereCondition($select, $storeIds, $urlRewriteAlias);

        if (!empty($orderFirstCondition)) {
            $select->order($orderFirstCondition);
        }

        if (!empty($orderSecondCondition)) {
            $select->order($orderSecondCondition);
        }

        if ($orderFirstCondition && $orderSecondCondition) {
            $select->order($urlRewriteAlias . '.url_rewrite_id ' . \Zend_Db_Select::SQL_ASC);
            $select->limit(1);
        }
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param array $storeIds
     * @param $alias
     * @return \Magento\Framework\DB\Select
     */
    protected function addUrlRewriteWhereCondition($select, $storeIds, $alias)
    {
        if (CanonicalType::URL_TYPE_NO_CATEGORIES === $this->urlType) {
            return $select->where(
                $alias . '.metadata IS NULL'
            );
        }

        $localCategoryCache = [];

        foreach ($storeIds as $storeId) {

            if (empty($this->storeRootCategories[$storeId])) {
                $this->storeRootCategories[$storeId] = $this->storeManager->getStore($storeId)->getRootCategoryId();
            }

            if ($this->storeRootCategories[$storeId]) {

                $categoryId = $this->storeRootCategories[$storeId];

                if (!in_array($categoryId, $localCategoryCache)) {
                    $localCategoryCache[] = $categoryId;
                    $select->where($alias . '.target_path NOT LIKE "%/category/' . (int)$categoryId . '"');
                }
            }
        }

        return $select;
    }
}