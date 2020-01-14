<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface CategoryFilterSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get category filter list
     *
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface[]
     */
    public function getItems();

    /**
     * Set category filter list
     *
     * @param \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
