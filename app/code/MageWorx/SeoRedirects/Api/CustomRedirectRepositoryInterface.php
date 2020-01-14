<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface;

/**
 * @api
 */
interface CustomRedirectRepositoryInterface
{
    /**
     * Save custom redirect
     *
     * @param CustomRedirectInterface $customRedirect
     * @return CustomRedirectInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CustomRedirectInterface $customRedirect);

    /**
     * Retrieve custom redirect
     *
     * @param int $customRedirectId
     * @return CustomRedirectInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($customRedirectId);

    /**
     * Retrieve custom redirects matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \MageWorx\SeoRedirects\Api\Data\DpRedirectSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete redirect
     *
     * @param CustomRedirectInterface $customRedirect
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CustomRedirectInterface $customRedirect);

    /**
     * Delete redirect by ID
     *
     * @param int $customRedirectId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($customRedirectId);
}
