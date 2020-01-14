<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Model\ResourceModel\Catalog;

use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use MageWorx\HtmlSitemap\Model\Source;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * HTML Sitemap resource product collection model
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
    protected $_attributesCache = [];

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
     * Sitemap config data
     *
     * @var \MageWorx\HtmlSitemap\Helper\Data
     */
    protected $sitemapHelper;

    /**
     * @var \MageWorx\HtmlSitemap\Helper\StoreUrl
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
     * @param \MageWorx\HtmlSitemap\Helper\Data $sitemapHelper
     * @param \MageWorx\HtmlSitemap\Helper\StoreUrl $storeUrlHelper
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
        \MageWorx\HtmlSitemap\Helper\Data $sitemapHelper,
        \MageWorx\HtmlSitemap\Helper\StoreUrl $storeUrlHelper,
        \MageWorx\SeoAll\Helper\LinkFieldResolver $linkFieldResolver,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->productResource = $productResource;
        $this->storeManager = $storeManager;
        $this->productVisibility = $productVisibility;
        $this->productStatus = $productStatus;
        $this->eavConfigFactory = $eavConfigFactory;
        $this->sitemapHelper = $sitemapHelper;
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

        $attribute = $this->_getAttribute($attributeCode);
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
        $adapter   = $this->getConnection();
        $attribute = $this->_getAttribute($attributeCode);
        $linkField = $this->linkFieldResolver->getLinkField(ProductInterface::class, 'entity_id');
        $this->select->joinLeft(
            ['t1_' . $attributeCode => $attribute['table']],
            'e.' . $linkField . ' = t1_' . $attributeCode . '.' . $linkField . ' AND ' . $adapter->quoteInto(
                ' t1_' . $attributeCode . '.store_id = ?',
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            ) . $adapter->quoteInto(
                ' AND t1_' . $attributeCode . '.attribute_id = ?',
                $attribute['attribute_id']
            ),
            ($addToResult && $attribute['is_global']) ? [$attributeCode => 't1_' . $attributeCode . '.value'] : []
        );

        if (!$attribute['is_global']) {
            $this->select->joinLeft(
                ['t2_' . $attributeCode => $attribute['table']],
                $this->getConnection()->quoteInto(
                    't1_' .
                    $attributeCode .
                    '.' . $linkField . ' = t2_' .
                    $attributeCode .
                    '.' . $linkField . ' AND t1_' .
                    $attributeCode .
                    '.attribute_id = t2_' .
                    $attributeCode .
                    '.attribute_id AND t2_' .
                    $attributeCode .
                    '.store_id = ?',
                    $storeId
                ),
                $addToResult ? [
                    $attributeCode => $this->select->getAdapter()->getCheckSql(
                        't2_' . $attributeCode . '.value_id > 0',
                        't2_' . $attributeCode . '.value',
                        't1_' . $attributeCode . '.value'
                    )] : []
            );
        }
    }

    /**
     * Get attribute data by attribute code
     *
     * @param string $attributeCode
     * @return array
     */
    protected function _getAttribute($attributeCode)
    {
        if (!isset($this->_attributesCache[$attributeCode])) {
            $attribute = $this->productResource->getAttribute($attributeCode);

            $this->_attributesCache[$attributeCode] = [
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id' => $attribute->getId(),
                'table' => $attribute->getBackend()->getTable(),
                'is_global' => $attribute->getIsGlobal() ==
                \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'backend_type' => $attribute->getBackendType(),
            ];
        }
        return $this->_attributesCache[$attributeCode];
    }

    /**
     * Get category collection array
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $storeId
     * @return array|bool
     */
    public function getCollection($categoryId = null, $storeId = null)
    {
        $products = [];
        /* @var $store \Magento\Store\Model\Store */
        $store = $this->storeManager->getStore($storeId);
        if (!$store) {
            return false;
        }

        $adapter = $this->getConnection();

        $this->select = $adapter->select()->from(
            ['e' => $this->getMainTable()],
            [$this->getIdFieldName()]
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
        ->where(
            'w.website_id = ?',
            $store->getWebsiteId()
        );

        if ($categoryId && $this->sitemapHelper->getProductUrlLength() == Source\UrlLength::USE_CATEGORIES_PATH) {
            $this->select->where('catalog_url_rewrite.category_id = ?', $categoryId);
        } else {
            $this->select->where('catalog_url_rewrite.category_id IS NULL');
            $this->select->joinInner(
                ['catalog_category_product' => $this->getTable('catalog_category_product')],
                'catalog_category_product.category_id = ' . $categoryId .
                ' AND catalog_category_product.product_id = e.entity_id',
                ['category_id']
            );
        }

        if ($this->sitemapHelper->getCatProdSortOrder() == Source\SortOrder::SORT_BY_NAME) {
            $this->select->order(['name'], \Zend_Db_Select::SQL_DESC);
        } elseif ($this->sitemapHelper->getCatProdSortOrder() == Source\SortOrder::SORT_BY_POSITION) {
            $this->select->order([$this->getIdFieldName()], \Zend_Db_Select::SQL_DESC);
        }

        $this->joinAttribute($store->getId(), 'name', true);
        $this->addFilter($store->getId(), 'visibility', $this->productVisibility->getVisibleInSiteIds(), 'in');
        $this->addFilter($store->getId(), 'status', $this->productStatus->getVisibleStatusIds(), 'in');
        $this->addFilter($store->getId(), 'in_html_sitemap', 1);

//        echo $this->select; exit;


        $query = $adapter->query($this->select);
        while ($row = $query->fetch()) {
            $product = $this->prepareProduct($row, $store->getId(), $categoryId);
            $products[$product->getId()] = $product;
        }

        return $products;
    }

    /**
     * Prepare product
     *
     * @param array $productRow
     * @param int $storeId
     * @return \Magento\Framework\DataObject
     */
    protected function prepareProduct(array $productRow, $storeId, $categoryId)
    {
        $product = new \Magento\Framework\DataObject();
        $product->addData($productRow);
        $product['id'] = $productRow[$this->getIdFieldName()];

        if (empty($productRow['url'])) {
            $productRow['url'] = 'catalog/product/view/id/' . $product->getId();
            if ($categoryId && $this->sitemapHelper->getProductUrlLength() == Source\UrlLength::USE_CATEGORIES_PATH) {
                $productRow['url'] .= '/category/' . $categoryId;
            }
        }
        $product->setUrl($this->storeUrlHelper->getUrl($productRow['url'], $storeId));

        return $product;
    }
}
