<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model\ResourceModel\Catalog;


use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Store\Model\Store;
use \Magento\Sitemap\Model\Source\Product\Image\IncludeImage;
use MageWorx\SeoAll\Model\Source\Product\CanonicalType;

/**
 * {@inheritdoc}
 */
class Product extends \Magento\Sitemap\Model\ResourceModel\Catalog\Product
{
    const URL_REWRITE_TABLE_ALIAS              = 'url_rewrite';
    const URL_REWRITE_TABLE_ALIAS_FOR_SUBQUERY = 'url_b';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $query;

    /**
     * @var \MageWorx\XmlSitemap\Helper\Data
     */
    protected $helperSitemap;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var bool
     */
    protected $readed = false;

    /**
     * @var bool
     */
    protected $flexibleCanonicalFlag;

    /**
     * Product constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Sitemap\Helper\Data $sitemapData
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\ResourceModel\Product\Gallery $mediaGalleryResourceModel
     * @param \Magento\Catalog\Model\Product\Gallery\ReadHandler $mediaGalleryReadHandler
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param \MageWorx\XmlSitemap\Helper\Data $helperSitemap
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param bool $flexibleCanonicalFlag
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Sitemap\Helper\Data $sitemapData,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\ResourceModel\Product\Gallery $mediaGalleryResourceModel,
        \Magento\Catalog\Model\Product\Gallery\ReadHandler $mediaGalleryReadHandler,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \MageWorx\XmlSitemap\Helper\Data $helperSitemap,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $flexibleCanonicalFlag = false,
        $connectionName = null
    ) {
        $this->helperSitemap             = $helperSitemap;
        $this->eventManager              = $eventManager;
        $this->flexibleCanonicalFlag     = $flexibleCanonicalFlag;

        parent::__construct(
            $context,
            $sitemapData,
            $productResource,
            $storeManager,
            $productVisibility,
            $productStatus,
            $mediaGalleryResourceModel,
            $mediaGalleryReadHandler,
            $mediaConfig,
            $connectionName
        );
    }

    /**
     * Additional condition related to flexible canonical functionality from SEO Base extension
     *
     * @return string
     */
    protected function getUrlRewriteWhereCondition()
    {
        if (!$this->flexibleCanonicalFlag) {
            return ' AND url_rewrite.metadata IS NULL';
        }

        return '';
    }


    /**
     * Get product collection array
     * Call this function while !isCollectionReaded() to read all collection
     *
     * @param null|string|bool|int|Store $storeId
     * @return \Magento\Framework\DataObject[]|null
     */
    public function getLimitedCollection($storeId, $limit)
    {
        $products = [];

        /* @var $store Store */
        $store = $this->_storeManager->getStore($storeId);
        if (!$store) {
            return false;
        }

        if ($limit <= 0) {
            return false;
        }

        if (!isset($this->query)) {
            $connection = $this->getConnection();

            $whereCondition = $this->getUrlRewriteWhereCondition();

            $this->_select = $connection->select()->from(
                ['e' => $this->getMainTable()],
                [
                    $this->getIdFieldName(),
                    $this->_productResource->getLinkField(),
                    'updated_at'
                ]
            )->joinInner(
                ['w' => $this->getTable('catalog_product_website')],
                'e.entity_id = w.product_id',
                []
            )->joinLeft(
                ['url_rewrite' => $this->getTable('url_rewrite')],
                'e.entity_id = url_rewrite.entity_id AND url_rewrite.is_autogenerated = 1' . $whereCondition
                . $connection->quoteInto(' AND url_rewrite.store_id = ?', $store->getId())
                . $connection->quoteInto(' AND url_rewrite.entity_type = ?', ProductUrlRewriteGenerator::ENTITY_TYPE),
                ['url' => 'request_path']
            )->where(
                'w.website_id = ?',
                $store->getWebsiteId()
            );

            $this->_addFilter($store->getId(), 'status', $this->_productStatus->getVisibleStatusIds(), 'in');
            $this->_addFilter($store->getId(), 'visibility', $this->_productVisibility->getVisibleInSiteIds(), 'in');
            $this->_addFilter($store->getId(), 'in_xml_sitemap', 1, '=');

            if ($this->helperSitemap->isExcludeOutOfStockProducts()) {
                $this->_select->joinInner(
                    ['cataloginventory' => $this->getTable('cataloginventory_stock_item')],
                    'e.entity_id = cataloginventory.product_id AND cataloginventory.is_in_stock = 1'
                );
            }

            $imageInclude = $this->_sitemapData->getProductImageIncludePolicy($store->getId());
            if (IncludeImage::INCLUDE_NONE != $imageInclude) {
                $this->_joinAttribute($store->getId(), 'name');

                $this->_select->columns(
                    [
                        'name' => $this->getConnection()
                                       ->getIfNullSql('t2_name.value', 't1_name.value')
                    ]
                );

                if (IncludeImage::INCLUDE_ALL == $imageInclude) {
                    $this->_joinAttribute($store->getId(), 'thumbnail');

                    $this->_select->columns(
                        [
                            'thumbnail' => $this->getConnection()->getIfNullSql(
                                't2_thumbnail.value',
                                't1_thumbnail.value'
                            ),
                        ]
                    );
                } elseif (IncludeImage::INCLUDE_BASE == $imageInclude) {
                    $this->_joinAttribute($store->getId(), 'image');
                    $this->_select->columns(
                        ['image' => $this->getConnection()->getIfNullSql('t2_image.value', 't1_image.value')]
                    );
                }
            }

            $this->eventManager->dispatch(
                'mageworx_xmlsitemap_product_generation_before',
                ['select' => $this->_select, 'store_id' => $storeId]
            );

//            echo $this->_select->__toString();  die();
            $this->query  = $connection->query($this->_select);
            $this->readed = false;
        }

        for ($i = 0; $i < $limit; $i++) {
            if (!$row = $this->query->fetch()) {
                $this->readed = true;
                break;
            }

            $product                     = $this->_prepareProduct($row, $store->getId());
            $products[$product->getId()] = $product;
        }

        return $products;
    }

    /**
     * @return bool
     */
    public function isCollectionReaded()
    {
        return $this->readed;
    }
}
