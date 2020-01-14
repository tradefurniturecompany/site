<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Model\Source;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;

/**
 * Used in creating options for config value selection
 *
 */
class Attributes extends \MageWorx\SeoMarkup\Model\Source
{
    /**
     *
     * @var array
     */
    protected $options;

    /**
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    protected $attributeCollection;

    /**
     *
     * @param AttributeCollection $attributeCollection
     */
    public function __construct(AttributeCollection $attributeCollection)
    {
        $this->attributeCollection = $attributeCollection;
    }

    /**
     *
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $options = [];
            foreach ($this->attributeCollection as $item) {
                $frontendLabel = $item->getData('frontend_label') ? ' (' .  $item->getData('frontend_label')  . ')' : '';
                $options[$item->getData('attribute_code')] = $item->getData('attribute_code') . $frontendLabel;
            }
            array_unshift($options, __('-- Please Select --'));
            $this->options = $options;
        }
        return $this->options;
    }
}
