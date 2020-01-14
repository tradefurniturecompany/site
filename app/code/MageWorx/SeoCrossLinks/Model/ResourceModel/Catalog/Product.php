<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Model\ResourceModel\Catalog;

use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * SeoCrossLinks resource product collection model
 */
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $productStatus;

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
     * Product constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Eav\Model\ConfigFactory $eavConfigFactory
     * @param \MageWorx\SeoCrossLinks\Helper\StoreUrl $storeUrlHelper
     * @param \MageWorx\SeoAll\Helper\LinkFieldResolver $linkFieldResolver
     * @param null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Eav\Model\ConfigFactory $eavConfigFactory,
        \MageWorx\SeoCrossLinks\Helper\StoreUrl $storeUrlHelper,
        \MageWorx\SeoAll\Helper\LinkFieldResolver $linkFieldResolver,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->productResource = $productResource;
        $this->storeManager = $storeManager;
        $this->productVisibility = $productVisibility;
        $this->productStatus = $productStatus;
        $this->eavConfigFactory = $eavConfigFactory;
        $this->storeUrlHelper = $storeUrlHelper;
        $this->linkFieldResolver = $linkFieldResolver;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_product_entity', 'entity_id');
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
        $linkField = $this->linkFieldResolver->getLinkField(ProductInterface::class, 'entity_id');
        $this->select->joinLeft(
            ['t1_' . $attributeCode => $attribute['table']],
            'e.'. $linkField .' = t1_' . $attributeCode . '.'. $linkField .' AND ' . $adapter->quoteInto(
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
                    '.'. $linkField .' = t2_' .
                    $attributeCode .
                    '.'. $linkField .' AND t1_' .
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
            $attribute = $this->productResource->getAttribute($attributeCode);

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
     * Get product (sku => array (URL & name)) array
     *
     * @param Mage_Core_Model_Store|int $store
     * @return array
     */
    public function getCollection($skuList, $store, $includeTitle = false)
    {
        $productUrls = array();

        if (empty($skuList)) {
            return $productUrls;
        }

        $products = [];
        /* @var $store \Magento\Store\Model\Store */
        if (!$store) {
            return false;
        }

        $storeId = $store->getStoreId();

        $adapter = $this->getConnection();

        $this->select = $adapter->select()->from(
            ['e' => $this->getMainTable()],
            [$this->getIdFieldName(), 'sku']
        )->joinInner(
            ['w' => $this->getTable('catalog_product_website')],
            'e.entity_id = w.product_id',
            []
        )->joinLeft(
            ['url_rewrite' => $this->getTable('url_rewrite')],
            'e.entity_id = url_rewrite.entity_id AND url_rewrite.is_autogenerated = 1'
            . $adapter->quoteInto(' AND url_rewrite.store_id = ?', $store->getId())
            . $adapter->quoteInto(' AND url_rewrite.entity_type = ?', ProductUrlRewriteGenerator::ENTITY_TYPE),
            ['url' => 'request_path']
        )->joinLeft(
            ['catalog_url_rewrite' => $this->getTable('catalog_url_rewrite_product_category')],
            'url_rewrite.url_rewrite_id = catalog_url_rewrite.url_rewrite_id',
            []
        )
        ->where('e.sku IN(?)', $skuList)
        //->where('catalog_url_rewrite.category_id IS NULL')
        ->where("url_rewrite.target_path NOT LIKE('%/category/%')")
        ->where(
            'w.website_id = ?',
            $store->getWebsiteId()
        );


        if ($includeTitle) {
            $this->joinAttribute($storeId, 'name', true);
        }
        $this->addFilter($storeId, 'visibility', $this->productVisibility->getVisibleInSiteIds(), 'in');
        $this->addFilter($storeId, 'status', $this->productStatus->getVisibleStatusIds(), 'in');

        $query = $adapter->query($this->select);
        while ($row = $query->fetch()) {
            $resArray = array();
            $resArray['url']  = $this->_prepareUrl($row, $storeId);
            $resArray['name'] = !empty($row['name']) ? $row['name'] : '';
            $products[$row['sku']] = $resArray;
        }

        return $products;
    }

    /**
     * Prepare product URL
     *
     * @param array $productRow
     * @param int $storeId
     * @return string
     */
    protected function _prepareUrl(array $productRow, $storeId)
    {
        if (!empty($productRow['url'])) {
            $partUrl = $productRow['url'];
        } else {
            $partUrl = 'catalog/product/view/id/' . $productRow[$this->getIdFieldName()];
        }

        $storeBaseUrl = $this->storeUrlHelper->getUrl($partUrl, $storeId);
        return $storeBaseUrl;
    }
}
