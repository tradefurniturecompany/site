<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoAll\Model\Source\Product;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as ProductAttributeCollectionFactory;
use MageWorx\SeoAll\Model\Source;

class Attribute extends Source
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    protected $_options;

    /**
     * @var ProductAttributeCollectionFactory
     */
    protected $productAttributeCollectionFactory;

    /**
     * Filter constructor
     * @param ProductAttributeCollectionFactory $productAttributeCollectionFactory
     */
    public function __construct(
        ProductAttributeCollectionFactory $productAttributeCollectionFactory
    ) {
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->_options === null) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $collection */
            $collection = $this->productAttributeCollectionFactory->create();
            $collection->addVisibleFilter();
            $collection->addIsFilterableFilter();

            $attributeArray = [];

            foreach ($collection as $attribute) {
                $attributeArray[] = [
                    'label' => $attribute->getData('frontend_label'),
                    'value' => $attribute->getData('attribute_id'),
                ];
            }
            $this->_options = $attributeArray;
        }
        return $this->_options;
    }
}
