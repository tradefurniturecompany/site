<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Observer;

/**
 * Observer class for conversion product template variables: category, categories
 */
class ConvertProductCategories implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Product\MetaTitle
     */
    protected $metaTitleConverter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Product\MetaDescription
     */
    protected $metaDescriptionConverter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Product\MetaKeywords
     */
    protected $metaKeywordsConverter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Product\ShortDescription
     */
    protected $shortDescriptionConverter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Product\Description
     */
    protected $descriptionConverter;

    /**
     * ConvertProductCategories constructor.
     *
     * @param \MageWorx\SeoXTemplates\Model\Converter\Product\MetaTitle        $metaTitleConverter
     * @param \MageWorx\SeoXTemplates\Model\Converter\Product\MetaDescription  $metaDescriptionConverter
     * @param \MageWorx\SeoXTemplates\Model\Converter\Product\MetaKeywords     $metaKeywordsConverter
     * @param \MageWorx\SeoXTemplates\Model\Converter\Product\ShortDescription $shortDescriptionConverter
     * @param \MageWorx\SeoXTemplates\Model\Converter\Product\Description      $descriptionConverter
     */
    public function __construct(
        \MageWorx\SeoXTemplates\Model\Converter\Product\MetaTitle $metaTitleConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Product\MetaDescription $metaDescriptionConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Product\MetaKeywords $metaKeywordsConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Product\ShortDescription $shortDescriptionConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Product\Description $descriptionConverter
    ) {
        $this->metaTitleConverter        = $metaTitleConverter;
        $this->metaDescriptionConverter  = $metaDescriptionConverter;
        $this->metaKeywordsConverter     = $metaKeywordsConverter;
        $this->shortDescriptionConverter = $shortDescriptionConverter;
        $this->descriptionConverter      = $descriptionConverter;
    }

    /**
     * Convert properties of the product that contain [category] and [categories]
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getData('product');

        if (!$product) {
            return;
        }

        /** @var \Magento\Catalog\Controller\Product\View $action */
        $action  = $observer->getData('controller_action');

        if ($action->getRequest()->getFullActionName() !== 'catalog_product_view') {
            return;
        }

        $metaTitle = $this->metaTitleConverter->convert($product, $product->getMetaTitle(), true);
        $product->setMetaTitle($metaTitle);

        $metaDescription = $this->metaDescriptionConverter->convert($product, $product->getMetaDescription(), true);
        $product->setMetaDescription($metaDescription);

        $metaKeyword = $this->metaKeywordsConverter->convert($product, $product->getMetaKeyword(), true);
        $product->setMetaKeyword($metaKeyword);

        $shortDescription = $this->shortDescriptionConverter->convert($product, $product->getShortDescription(), true);
        $product->setShortDescription($shortDescription);

        $description = $this->descriptionConverter->convert($product, $product->getDescription(), true);
        $product->setDescription($description);
    }
}
