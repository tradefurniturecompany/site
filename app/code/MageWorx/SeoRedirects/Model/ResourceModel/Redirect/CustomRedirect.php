<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\ResourceModel\Redirect;

use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect as CustomRedirectModel;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\CollectionFactory as RedirectCollectionFactory;

/**
 * CustomRedirect resource
 */
class CustomRedirect extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /** @var RedirectCollectionFactory */
    protected $redirectCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $redirectTable;

    /**
     * CustomRedirect constructor.
     *
     * @param RedirectCollectionFactory $redirectCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(
        RedirectCollectionFactory $redirectCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        $this->storeManager              = $storeManager;
        $this->redirectCollectionFactory = $redirectCollectionFactory;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoredirects_redirect_custom', 'redirect_id');
        $this->redirectTable = $this->getTable('mageworx_seoredirects_redirect_custom');
    }

    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var \MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\Collection $collection */
        $collection = $this->redirectCollectionFactory->create();
        $collection->addStoreFilter($object->getStoreId());
        $collection->addRequestEntityFilter($object->getRequestEntityIdentifier(), $object->getRequestEntityType());

        /** @var \MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface $item */
        $item = $collection->getFirstItem();

        if ($item->getId() && $object->getId() != $item->getId()) {
            $storeName = $this->storeManager->getStore($item->getStoreId())->getName();

            switch ($item->getRequestEntityType()) {
                case CustomRedirectModel::REDIRECT_TYPE_CUSTOM:
                    $entityType = __('URL');
                    $identifier = $item->getRequestEntityIdentifier();
                    break;
                case CustomRedirectModel::REDIRECT_TYPE_PRODUCT:
                    $entityType = __('Product');
                    $identifier = 'ID#' . $item->getRequestEntityIdentifier();
                    break;
                case CustomRedirectModel::REDIRECT_TYPE_CATEGORY:
                    $entityType = __('Category');
                    $identifier = 'ID#' . $item->getRequestEntityIdentifier();
                    break;
                case CustomRedirectModel::REDIRECT_TYPE_CUSTOM:
                    $entityType = __('Page');
                    $identifier = 'ID#' . $item->getRequestEntityIdentifier();
                    break;
            }

            throw new LocalizedException(
                __(
                    "Request Entity Type - %entityType (%identifier) for store \"%storeName\" already exists. Redirect wasn't saved",
                    [
                        'entityType' => $entityType,
                        'identifier' => $identifier,
                        'storeName'  => $storeName
                    ]
                )
            );
        }

        parent::save($object);
    }

    /**
     * Perform actions before object save
     *
     * @param AbstractModel|\MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface $object
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setData('updated_at', time());
        parent::_beforeSave($object);

        return $this;
    }

    /**
     * Delete redirects by product id.
     *
     * @param int $productId
     * @return $this
     */
    public function deleteRedirectsByProductId($productId)
    {
        return $this->deleteRedirectsByEntity($productId, CustomRedirectModel::REDIRECT_TYPE_PRODUCT);
    }

    /**
     * Delete redirects by category id.
     *
     * @param int $categoryId
     * @return $this
     */
    public function deleteRedirectsByCategoryId($categoryId)
    {
        return $this->deleteRedirectsByEntity($categoryId, CustomRedirectModel::REDIRECT_TYPE_CATEGORY);
    }

    /**
     * Delete redirects by page id.
     *
     * @param int $pageId
     * @return $this
     */
    public function deleteRedirectsByPageId($pageId)
    {
        return $this->deleteRedirectsByEntity($pageId, CustomRedirectModel::REDIRECT_TYPE_PAGE);
    }

    /**
     * @param int $entityId
     * @param int $entityType
     * @return $this
     */
    public function deleteRedirectsByEntity($entityId, $entityType)
    {
        $this->getConnection()->delete(
            $this->redirectTable,
            [
                CustomRedirectModel::REQUEST_ENTITY_IDENTIFIER . '=?' => $entityId,
                CustomRedirectModel::REQUEST_ENTITY_TYPE . '=?'       => $entityType
            ]
        );
        $this->getConnection()->delete(
            $this->redirectTable,
            [
                CustomRedirectModel::TARGET_ENTITY_IDENTIFIER . '=?' => $entityId,
                CustomRedirectModel::TARGET_ENTITY_TYPE . '=?'       => $entityType
            ]
        );

        return $this;
    }
}
