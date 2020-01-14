<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use MageWorx\SeoRedirects\Api\Data\DpRedirectInterface;

/**
 * @api
 */
interface DpRedirectRepositoryInterface
{
    /**
     * Save deleted product redirect
     *
     * @param DpRedirectInterface $dpRedirect
     * @return DpRedirectInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(DpRedirectInterface $dpRedirect);

    /**
     * Retrieve deleted product redirect
     *
     * @param int $dpRedirectId
     * @return DpRedirectInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dpRedirectId);

    /**
     * Retrieve deleted product redirects matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \MageWorx\SeoRedirects\Api\Data\DpRedirectSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete redirect
     *
     * @param DpRedirectInterface $dpRedirect
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(DpRedirectInterface $dpRedirect);

    /**
     * Delete redirect by ID
     *
     * @param int $dpRedirectId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($dpRedirectId);
}
