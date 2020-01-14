<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;
use Magento\Catalog\Model\Product\Attribute\Source\Status as SourceProductStatus;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Model\ResourceModel\Product as ResourceProduct;
use Magento\Catalog\Model\ResourceModel\Category as ResourceCategory;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsPageCollectionFactory;
use MageWorx\SeoBase\Api\Data\CustomCanonicalInterface;
use MageWorx\SeoBase\Model\Source\CustomCanonical\TargetStoreId;
use MageWorx\SeoBase\Helper\StoreUrl as HelperStoreUrl;
use \MageWorx\SeoBase\Model\ResourceModel\Catalog\Product\FlexibleCanonical as FlexibleCanonicalResource;

class CustomCanonical extends AbstractDb
{
    /**
     * @var ResourceProduct
     */
    private $resourceProduct;

    /**
     * @var ProductVisibility
     */
    private $productVisibility;

    /**
     * @var ResourceCategory
     */
    private $resourceCategory;

    /**
     * @var CmsPageCollectionFactory
     */
    private $cmsPageCollectionFactory;

    /**
     * @var HelperStoreUrl
     */
    private $helperStoreUrl;

    /**
     * @var \MageWorx\SeoBase\Model\ResourceModel\Catalog\Product\FlexibleCanonical
     */
    protected $flexibleCanonicalResource;

    /**
     * CustomCanonical constructor.
     *
     * @param Context $context
     * @param ResourceProduct $resourceProduct
     * @param ProductVisibility $productVisibility
     * @param ResourceCategory $resourceCategory
     * @param CmsPageCollectionFactory $cmsPageCollectionFactory
     * @param HelperStoreUrl $helperStoreUrl
     * @param FlexibleCanonicalResource $flexibleCanonical
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        ResourceProduct $resourceProduct,
        ProductVisibility $productVisibility,
        ResourceCategory $resourceCategory,
        CmsPageCollectionFactory $cmsPageCollectionFactory,
        HelperStoreUrl $helperStoreUrl,
        FlexibleCanonicalResource $flexibleCanonical,
        $connectionName = null
    ) {
        $this->resourceProduct           = $resourceProduct;
        $this->productVisibility         = $productVisibility;
        $this->resourceCategory          = $resourceCategory;
        $this->cmsPageCollectionFactory  = $cmsPageCollectionFactory;
        $this->helperStoreUrl            = $helperStoreUrl;
        $this->flexibleCanonicalResource = $flexibleCanonical;

        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_seobase_custom_canonical', 'entity_id');
    }

    /**
     * @param string $entityType
     * @param string|int $entityId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteCustomCanonicalsByEntity($entityType, $entityId)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            [
                CustomCanonicalInterface::SOURCE_ENTITY_TYPE . '=?' => $entityType,
                CustomCanonicalInterface::SOURCE_ENTITY_ID . '=?'   => $entityId
            ]
        );
        $this->getConnection()->delete(
            $this->getMainTable(),
            [
                CustomCanonicalInterface::TARGET_ENTITY_TYPE . '=?' => $entityType,
                CustomCanonicalInterface::TARGET_ENTITY_ID . '=?'   => $entityId
            ]
        );
    }

    /**
     * @param CustomCanonicalInterface $customCanonical
     * @param $currentStoreId
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function getUrl($customCanonical, $currentStoreId)
    {
        $targetEntityType = $customCanonical->getTargetEntityType();
        $targetEntityId   = $customCanonical->getTargetEntityId();

        if ($customCanonical->getTargetStoreId() == TargetStoreId::SAME_AS_SOURCE_ENTITY) {
            $storeId = $customCanonical->getSourceStoreId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID
                ? $currentStoreId
                : $customCanonical->getSourceStoreId();
        } else {
            $storeId = $customCanonical->getTargetStoreId();
        }

        switch ($targetEntityType) {
            case Rewrite::ENTITY_TYPE_CUSTOM:
                $url = $targetEntityId;

                if (in_array(substr($url, 0, 6), ['http:/', 'https:'])) {
                    return $url;
                }
                break;
            case Rewrite::ENTITY_TYPE_CMS_PAGE:
                $url = $this->getTargetCmsPageIdentifier($targetEntityId, $storeId);
                break;
            default:
                if ($this->isActiveTargetCatalogEntity($targetEntityType, $targetEntityId, $storeId)) {
                    $url = $this->getTargetCatalogEntityUrl($targetEntityType, $targetEntityId, $storeId);
                } else {
                    return null;
                }
        }

        return !empty($url) ? $this->helperStoreUrl->getUrl($url, $storeId) : null;
    }

    /**
     * @param string $entityId
     * @param string|int $storeId
     * @return string|null
     */
    private function getTargetCmsPageIdentifier($entityId, $storeId)
    {
        /** @var \Magento\Cms\Model\ResourceModel\Page\Collection $collection */
        $collection = $this->cmsPageCollectionFactory->create();

        $collection
            ->addStoreFilter($storeId)
            ->addFieldToFilter(
                'page_id',
                ['eq' => $entityId]
            );

        $cmsPage = $collection->getFirstItem();

        return $cmsPage->getData('identifier');
    }

    /**
     * @param string $entityType
     * @param string $entityId
     * @param string|int $storeId
     * @return string|null
     * @throws \Zend_Db_Statement_Exception
     */
    private function getTargetCatalogEntityUrl($entityType, $entityId, $storeId)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select();

        $select->from(
            ['url_rewrite' => $this->getTable('url_rewrite')],
            ['url' => 'request_path']
        )->where(
            'url_rewrite.entity_type = ?',
            $entityType
        )->where(
            'url_rewrite.entity_id = ?',
            $entityId
        )->where(
            'url_rewrite.store_id = ?',
            $storeId
        )->where(
            'url_rewrite.is_autogenerated = 1'
        );

        if ($entityType == Rewrite::ENTITY_TYPE_PRODUCT) {
            $this->flexibleCanonicalResource->addFlexibleConditions($select, $storeId);
        }

        $row = $adapter->query($select)->fetch();

        if (!is_array($row)) {
            return null;
        }

        return !empty($row['url']) ? $row['url'] : null;
    }

    /**
     * @param string $entityType
     * @param string $entityId
     * @param string|int $storeId
     * @return bool
     */
    private function isActiveTargetCatalogEntity($entityType, $entityId, $storeId)
    {
        if ($entityType == Rewrite::ENTITY_TYPE_PRODUCT) {
            $attributesData = $this->resourceProduct->getAttributeRawValue(
                $entityId,
                ['status', 'visibility'],
                $storeId
            );

            return $attributesData['status'] == SourceProductStatus::STATUS_ENABLED
                && in_array($attributesData['visibility'], $this->productVisibility->getVisibleInSiteIds());
        }

        if ($entityType == Rewrite::ENTITY_TYPE_CATEGORY) {
            return $this->resourceCategory->getAttributeRawValue($entityId, 'is_active', $storeId) == 1;
        }

        return false;
    }
}
