<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Model\Source\Product;

class AttributeOption
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    protected $_options;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    protected $attributeOptionCollection;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository
     */
    protected $productAttributeRepository;

    /**
     * Attribute constructor.
     *
     * @param \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $attributeOptionCollection
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->attributeOptionCollection  = $attributeOptionCollection;
    }

    /**
     * @return array
     */
    public function toOptionArray($attributeId)
    {
        if ($this->_options === null) {

            $this->_options = $this->attributeOptionCollection
                ->setPositionOrder('asc')
                ->setAttributeFilter($attributeId)
                ->setStoreFilter()
                ->load()
                ->toOptionArray();
        }

        array_unshift($this->_options, ['value' => '0', 'label' =>  __('All Values')]);

        return $this->_options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray($attributeId)
    {
        $_tmpOptions = $this->toOptionArray($attributeId);
        $_options    = [];
        foreach ($_tmpOptions as $option) {
            $_options[$option['value']] = $option['label'];
        }

        return $_options;
    }
}

