<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Api;

/**
 * @api
 */
interface ItemRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\MegaMenu\Api\Data\Menu\ItemInterface $item
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function save(\Amasty\MegaMenu\Api\Data\Menu\ItemInterface $item);

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Get by entity id & store id
     *
     * @param int $entityId
     * @param int $storeId
     * @param string $type
     *
     * @return Data\Menu\ItemInterface
     */
    public function getByEntityId($entityId, $storeId, $type);

    /**
     * Delete
     *
     * @param \Amasty\MegaMenu\Api\Data\Menu\ItemInterface $item
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\MegaMenu\Api\Data\Menu\ItemInterface $item);

    /**
     * Delete by id
     *
     * @param int $entityId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($entityId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
