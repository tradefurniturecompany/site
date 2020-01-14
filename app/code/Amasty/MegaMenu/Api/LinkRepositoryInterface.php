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
interface LinkRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\MegaMenu\Api\Data\Menu\LinkInterface $link
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\LinkInterface
     */
    public function save(\Amasty\MegaMenu\Api\Data\Menu\LinkInterface $link);

    /**
     * Get by id
     *
     * @param int $entityId
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\LinkInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($entityId);

    /**
     * Delete
     *
     * @param \Amasty\MegaMenu\Api\Data\Menu\LinkInterface $link
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\MegaMenu\Api\Data\Menu\LinkInterface $link);

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
