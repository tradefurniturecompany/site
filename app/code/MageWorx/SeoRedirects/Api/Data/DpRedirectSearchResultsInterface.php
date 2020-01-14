<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface DpRedirectSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get deleted product redirect list.
     *
     * @return \MageWorx\SeoRedirects\Api\Data\DpRedirectInterface[]
     */
    public function getItems();

    /**
     * Set deleted product redirect list.
     *
     * @param \MageWorx\SeoRedirects\Api\Data\DpRedirectInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
