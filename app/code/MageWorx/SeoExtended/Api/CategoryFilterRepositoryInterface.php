<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Api;

/**
 * @api
 */
interface CategoryFilterRepositoryInterface
{
    /**
     * Save SEO category filter
     *
     * @param \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface $seoCategoryFilter
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\MageWorx\SeoExtended\Api\Data\CategoryFilterInterface $seoCategoryFilter);

    /**
     * Retrieve category filter
     *
     * @param int $id
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Delete SEO category filter
     *
     * @param \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface $seoCategoryFilter
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\MageWorx\SeoExtended\Api\Data\CategoryFilterInterface $seoCategoryFilter);

    /**
     * Delete SEO category filter by ID
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
