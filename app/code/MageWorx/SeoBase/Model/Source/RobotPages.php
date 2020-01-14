<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Source;

class RobotPages
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @param bool $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        $this->options = [
            ['value' => '^checkout_.+', 'label' => __('Checkout Pages')],
            ['value' => '^contact_.+', 'label' => __('Contact Us Page')],
            ['value' => '^customer_.+', 'label' => __('Customer Account Pages')],
            ['value' => '^catalog_product_compare_.+', 'label' => __('Product Compare Pages')],
            ['value' => '^rss_.+', 'label' => __('RSS Feeds')],
            ['value' => '^catalogsearch_.+', 'label' => __('Search Pages')],
            ['value' => '.*?_product_send$', 'label' => __('Send Product Pages')],
            ['value' => '^wishlist_.+', 'label' => __('Wishlist Pages')],
        ];

        $options = $this->options;
        if (!$isMultiselect) {
            array_unshift(
                $options,
                ['value' => '', 'label' => __('--Please Select--')]
            );
        }
        return $options;
    }
}
