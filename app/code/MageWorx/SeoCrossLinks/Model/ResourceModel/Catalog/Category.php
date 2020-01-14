<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Model\ResourceModel\Catalog;

use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\Catalog\Api\Data\CategoryInterface;

/**
 * SeoCrossLinks resource category collection model
 */
class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Collection Zend Db select
     *
     * @var \Zend_Db_Select
     */
    protected $select;

    /**
     * Attribute cache
     *
     * @var array
     */
    protected $attributesCache = [];

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category $categoryResource
     */
    protected $categoryResource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Eav\Model\ConfigFactory
     */
    protected $eavConfigFactory;

    /**
     * @var \MageWorx\SeoCrossLinks\Helper\StoreUrl
     */
    protected $storeUrlHelper;

    /**
     * @var \MageWorx\SeoAll\Helper\LinkFieldResolver
     */
    protected $linkFieldResolver;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category $categoryResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\ConfigFactory $eavConfigFactory
     * @param \MageWorx\SeoCrossLinks\Helper\StoreUrl $storeUrlHelper
     * @param string|null $resourcePrefix
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category $categoryResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\ConfigFactory $eavConfigFactory,
        \MageWorx\SeoCrossLinks\Helper\StoreUrl $storeUrlHelper,
        \MageWorx\SeoAll\Helper\LinkFieldResolver $linkFieldResolver,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->categoryResource = $categoryResource;
        $this->storeManager = $storeManager;
        $this->eavConfigFactory = $eavConfigFactory;
        $this->storeUrlHelper = $storeUrlHelper;
        $this->linkFieldResolver = $linkFieldResolver;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_category_entity', 'entity_id');
    }

    /**
     * Add attribute to filter
     *
     * @param int $storeId
     * @param string $attributeCode
     * @param mixed $value
     * @param string $type
     * @return \Zend_Db_Select|bool
     */
    protected function addFilter($storeId, $attributeCode, $value, $type = '=')
    {
        if (!$this->select instanceof \Zend_Db_Select) {
            return false;
        }

        switch ($type) {
            case '=':
                $conditionRule = '=?';
                break;
            case 'in':
                $conditionRule = ' IN(?)';
                break;
            default:
                return false;
        }

        $attribute = $this->getAttribute($attributeCode);
        if ($attribute['backend_type'] == 'static') {
            $this->select->where('e.' . $attributeCode . $conditionRule, $value);
        } else {
            $this->joinAttribute($storeId, $attributeCode);
            if ($attribute['is_global']) {
                $this->select->where('t1_' . $attributeCode . '.value' . $conditionRule, $value);
            } else {
                $ifCase = $this->select->getAdapter()->getCheckSql(
                    't2_' . $attributeCode . '.value_id > 0',
                    't2_' . $attributeCode . '.value',
                    't1_' . $attributeCode . '.value'
                );
                $this->select->where('(' . $ifCase . ')' . $conditionRule, $value);
            }
        }

        return $this->select;
    }

    /**
     * Join attribute by code
     *
     * @param int $storeId
     * @param string $attributeCode
     * @param string $addToResult
     * @return void
     */
    protected function joinAttribute($storeId, $attributeCode, $addToResult = false)
    {
        $adapter = $this->getConnection();
        $attribute = $this->getAttribute($attributeCode);
        $linkField = $this->linkFieldResolver->getLinkField(CategoryInterface::class, 'entity_id');
        $this->select->joinLeft(
            ['t1_' . $attributeCode => $attribute['table']],
            'e.'.$linkField.' = t1_' . $attributeCode . '.'.$linkField.' AND ' . $adapter->quoteInto(
                ' t1_' . $attributeCode . '.store_id = ?',
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            ) . $adapter->quoteInto(
                ' AND t1_' . $attributeCode . '.attribute_id = ?',
                $attribute['attribute_id']
            ),
            $addToResult ? [$attributeCode => 't1_' . $attributeCode . '.value'] : []
        );

        if (!$attribute['is_global']) {
            $this->select->joinLeft(
                ['t2_' . $attributeCode => $attribute['table']],
                $this->getConnection()->quoteInto(
                    't1_' .
                    $attributeCode .
                    '.'.$linkField.' = t2_' .
                    $attributeCode .
                    '.'.$linkField.' AND t1_' .
                    $attributeCode .
                    '.attribute_id = t2_' .
                    $attributeCode .
                    '.attribute_id AND t2_' .
                    $attributeCode .
                    '.store_id = ?',
                    $storeId
                ),
                []
            );
        }
    }

    /**
     * Get attribute data by attribute code
     *
     * @param string $attributeCode
     * @return array
     */
    protected function getAttribute($attributeCode)
    {
        if (!isset($this->attributesCache[$attributeCode])) {
            $attribute = $this->categoryResource->getAttribute($attributeCode);

            $this->attributesCache[$attributeCode] = [
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id' => $attribute->getId(),
                'table' => $attribute->getBackend()->getTable(),
                'is_global' => $attribute->getIsGlobal() ==
                \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'backend_type' => $attribute->getBackendType(),
            ];
        }
        return $this->attributesCache[$attributeCode];
    }

    /**
     * Get category (id => array(URL & name)) array
     *
     * @param Mage_Core_Model_Store|int $store
     * @return array
     */
    public function getCollection(array $categoryIds, $store, $includeTitle = false)
    {
        if (empty($categoryIds)) {
            return array();
        }

        /* @var $store \Magento\Store\Model\Store */
        if (!$store) {
            return false;
        }

        $categories = [];

        $storeId = $store->getStoreId();
        $adapter = $this->getConnection();

        $this->select = $adapter->select()->from(
            ['e' => $this->getMainTable()],
            [$this->getIdFieldName(), 'parent_id', 'path', 'position', 'level', 'children_count']
        )->joinLeft(
            ['url_rewrite' => $this->getTable('url_rewrite')],
            'e.entity_id = url_rewrite.entity_id AND url_rewrite.is_autogenerated = 1'
            . $adapter->quoteInto(' AND url_rewrite.store_id = ?', $store->getId())
            . $adapter->quoteInto(' AND url_rewrite.entity_type = ?', CategoryUrlRewriteGenerator::ENTITY_TYPE),
            ['url' => 'request_path']
        )
        ->where('e.' . $this->getIdFieldName() . ' IN(?)', $categoryIds);

        if ($includeTitle) {
            $this->joinAttribute($storeId, 'name', true);
        }

        $query = $adapter->query($this->select);
        while ($row = $query->fetch()) {
            $resArray = array();
            $resArray['url']  = $this->_prepareUrl($row, $storeId);
            $resArray['name'] = !empty($row['name']) ? $row['name'] : '';
            $categories[$row['entity_id']] = $resArray;
        }

        return $categories;
    }

    /**
     * Prepare category URL
     *
     * @param array $categoryRow
     * @param int $storeId
     * @return string
     */
    protected function _prepareUrl(array $categoryRow, $storeId)
    {
        if (!empty($categoryRow['url'])) {
            $partUrl = $categoryRow['url'];
        } else {
            $partUrl = 'catalog/category/view/id/' . $categoryRow[$this->getIdFieldName()];
        }

        $storeBaseUrl = $this->storeUrlHelper->getUrl($partUrl, $storeId);
        return $storeBaseUrl;
    }
}
