<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Api;

use MageWorx\SeoBase\Api\Data\CustomCanonicalInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;

/**
 * @api
 */
interface CustomCanonicalRepositoryInterface
{
    /**
     * Save Custom Canonical
     *
     * @param CustomCanonicalInterface $customCanonical
     * @return CustomCanonicalInterface
     * @throws CouldNotSaveException
     */
    public function save(CustomCanonicalInterface $customCanonical);

    /**
     * Retrieve Custom Canonical by ID
     *
     * @param int $customCanonicalId
     * @return CustomCanonicalInterface
     * @throws NoSuchEntityException
     */
    public function getById($customCanonicalId);

    /**
     * @param string $entityType
     * @param int $entityId
     * @param int $storeId
     * @param bool $forSpecificStoreId
     * @return CustomCanonicalInterface|null
     * @throws NoSuchEntityException
     */
    public function getBySourceEntityData($entityType, $entityId, $storeId, $forSpecificStoreId = true);

    /**
     * Get empty Custom Canonical
     *
     * @return CustomCanonicalInterface
     */
    public function getEmptyEntity();

    /**
     * Delete Custom Canonical
     *
     * @param CustomCanonicalInterface $customCanonical
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(CustomCanonicalInterface $customCanonical);

    /**
     * Delete Custom Canonical by ID
     *
     * @param int $customCanonicalId
     * @return bool true on success
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($customCanonicalId);

    /**
     * Cleanup Custom Canonicals after Entity delete
     *
     * @param string $entityType
     * @param int $entityId
     * @return void
     */
    public function deleteCustomCanonicalsByEntity($entityType, $entityId);

    /**
     * @param CustomCanonicalInterface $customCanonical
     * @param int $currentStoreId
     * @return string|null
     */
    public function getCustomCanonicalUrl($customCanonical, $currentStoreId);
}
