<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model;

use MageWorx\SeoBase\Api\CustomCanonicalRepositoryInterface;
use MageWorx\SeoBase\Api\Data\CustomCanonicalInterface;
use MageWorx\SeoBase\Model\ResourceModel\CustomCanonical as ResourceCustomCanonical;
use MageWorx\SeoBase\Model\ResourceModel\CustomCanonical\CollectionFactory;
use MageWorx\SeoBase\Model\ResourceModel\CustomCanonical\Collection as CustomCanonicalCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

class CustomCanonicalRepository implements CustomCanonicalRepositoryInterface
{
    /**
     * @var ResourceCustomCanonical
     */
    private $resourceModel;

    /**
     * @var CustomCanonicalFactory
     */
    private $customCanonicalFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * CustomCanonicalRepository constructor.
     *
     * @param ResourceCustomCanonical $resourceModel
     * @param CustomCanonicalFactory $customCanonicalFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        ResourceCustomCanonical $resourceModel,
        CustomCanonicalFactory $customCanonicalFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->resourceModel          = $resourceModel;
        $this->customCanonicalFactory = $customCanonicalFactory;
        $this->collectionFactory      = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CustomCanonicalInterface $customCanonical)
    {
        try {
            $this->resourceModel->save($customCanonical);
        } catch (\Exception $e) {

            if (in_array($e->getCode(), [1062, 23000])
                && preg_match('#SQLSTATE\[23000\]: [^:]+: 1062[^\d]#', $e->getMessage())
            ) {
                $message = __('Custom Canonical for this source entity already exists');
            } else {
                $message = __('Could not save the Custom Canonical URL: %1', $e->getMessage());
            }

            throw new CouldNotSaveException($message, $e);
        }

        return $customCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($customCanonicalId)
    {
        /** @var CustomCanonical $customCanonical */
        $customCanonical = $this->customCanonicalFactory->create();
        $this->resourceModel->load($customCanonical, $customCanonicalId);

        if (!$customCanonical->getId()) {
            throw new NoSuchEntityException(__('Custom Canonical with ID "%1" does not exist.', $customCanonicalId));
        }

        return $customCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getBySourceEntityData($entityType, $entityId, $storeId, $forSpecificStoreId = true)
    {
        if ($storeId != \Magento\Store\Model\Store::DEFAULT_STORE_ID
            && !$forSpecificStoreId) {
            $condition = ['in' => [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId]];
        } else {
            $condition = ['eq' => $storeId];
        }

        /** @var CustomCanonicalCollection $collection */
        $collection = $this->collectionFactory->create();

        $collection
            ->addFieldToFilter(
                CustomCanonicalInterface::SOURCE_ENTITY_TYPE,
                ['eq' => $entityType]
            )->addFieldToFilter(
                CustomCanonicalInterface::SOURCE_ENTITY_ID,
                ['eq' => $entityId]
            )->addFieldToFilter(
                CustomCanonicalInterface::SOURCE_STORE_ID,
                $condition
            )->setOrder(CustomCanonicalInterface::SOURCE_STORE_ID);

        $item = $collection->getFirstItem();

        if (!$item->getData(CustomCanonicalInterface::ENTITY_ID)) {
            return null;
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmptyEntity()
    {
        return $this->customCanonicalFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CustomCanonicalInterface $customCanonical)
    {
        try {
            $this->resourceModel->delete($customCanonical);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __('Could not delete the Custom Canonical URL: %1', $e->getMessage()),
                $e
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($customCanonicalId)
    {
        return $this->delete($this->getById($customCanonicalId));
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomCanonicalUrl($customCanonical, $currentStoreId)
    {
        return $this->resourceModel->getUrl($customCanonical, $currentStoreId);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCustomCanonicalsByEntity($entityType, $entityId)
    {
        $this->resourceModel->deleteCustomCanonicalsByEntity($entityType, $entityId);
    }
}
