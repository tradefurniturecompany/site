<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface CustomRedirectSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get custom redirect list.
     *
     * @return \MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface[]
     */
    public function getItems();

    /**
     * Set custom redirect list.
     *
     * @param \MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
