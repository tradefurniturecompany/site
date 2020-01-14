<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoMarkup\Plugin;

class DisableDefaultReviewMarkupPlugin
{
    /**
     * @var \MageWorx\SeoMarkup\Helper\Product
     */
    protected $productHelper;

    /**
     * DisableDefaultReviewMarkup constructor.
     *
     * @param \MageWorx\SeoMarkup\Helper\Product $productHelper
     */
    public function __construct(
        \MageWorx\SeoMarkup\Helper\Product $productHelper
    ) {
        $this->productHelper = $productHelper;
    }

    /**
     * @param \Magento\Review\Block\Product\ReviewRenderer $subject
     * @param $result
     * @return mixed
     */
    public function afterToHtml($subject, $result)
    {
        if ($this->productHelper->isDisableDefaultReview()) {
            $result = str_replace('itemprop="aggregateRating"', '', $result);
            $result = str_replace('itemscope', '', $result);
            $result = str_replace('itemtype="http://schema.org/AggregateRating"', '', $result);
        }

        return $result;
    }
}