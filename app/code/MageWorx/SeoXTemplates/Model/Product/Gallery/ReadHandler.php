<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Product\Gallery;

use Magento\Catalog\Model\Product;

/**
 * Read handler for SEO Gallery Template
 */
class ReadHandler extends \Magento\Catalog\Model\Product\Gallery\ReadHandler
{
    /**
     * @var \MageWorx\SeoXTemplates\Model\ResourceModel\Product\Gallery
     */
    protected $resourceModel;

    /**
     * ReadHandler constructor.
     *
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
     * @param \MageWorx\SeoXTemplates\Model\ResourceModel\Product\Gallery $resourceModel
     */
    public function __construct(
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \MageWorx\SeoXTemplates\Model\ResourceModel\Product\Gallery $resourceModel
    ) {
        parent::__construct($attributeRepository, $resourceModel);
    }


    /**
     * @param Product $product
     * @param array $mediaEntries
     * @return void
     */
    public function addMediaDataToProduct(Product $product, array $mediaEntries)
    {
        $attrCode        = $this->getAttribute()->getAttributeCode();
        $value           = [];
        $value['images'] = [];
        $value['values'] = [];

        foreach ($mediaEntries as $mediaEntry) {
            $value['images'][$mediaEntry['value_id']] = $mediaEntry;
        }
        $product->setData($attrCode, $value);
    }
}
