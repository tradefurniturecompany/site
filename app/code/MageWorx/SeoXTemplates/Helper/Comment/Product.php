<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Helper\Comment;

use MageWorx\SeoXTemplates\Model\Template\Product as ProductTemplate;

/**
 * SEO XTemplates data helper
 */
class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param ProductTemplate $type
     * Return comments for category template
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    public function getComments($type)
    {
        $comment = '<br><small>' . $this->getVariablesComment() . $this->getRandomizerComment();
        switch ($type) {
            case ProductTemplate::TYPE_PRODUCT_SHORT_DESCRIPTION:
            case ProductTemplate::TYPE_PRODUCT_DESCRIPTION:
            case ProductTemplate::TYPE_PRODUCT_META_DESCRIPTION:
                $comment .= $this->getAdditionalCategoryComment();
                $comment .= $this->getDescriptionExample();
                break;
            case ProductTemplate::TYPE_PRODUCT_META_KEYWORDS:
                $comment .= $this->getAdditionalCategoryComment();
                $comment .= $this->getKeywordsExample();
                break;
            case ProductTemplate::TYPE_PRODUCT_SEO_NAME:
                $comment .= $this->getSeoNameExample();
                break;
            case ProductTemplate::TYPE_PRODUCT_URL_KEY:
                $comment .= $this->getUrlExample();
                break;
            case ProductTemplate::TYPE_PRODUCT_META_TITLE:
                $comment .= $this->getAdditionalCategoryComment();
                $comment .= $this->getMetaTitleExample();
                break;
            case ProductTemplate::TYPE_PRODUCT_GALLERY:
                $comment .= $this->getAdditionalGalleryComment();
                $comment .= $this->getGalleryExample();
                break;
            default:
                throw new \UnexpectedValueException(__('SEO XTemplates: Unknow Product Template Type'));
        }

        return $comment.'</small>';
    }

    /**
     * Return comment for url variables
     *
     * @return string
     */
    protected function getVariablesComment()
    {
        return '<p><p><b>' . __('Template variables') . '</b><br>' .
            '<p>[attribute] — e.g. [name], [price], [manufacturer], [color] — '
            . __('will be replaced with the respective product attribute value or removed if value is not available') . '<br>' .
            '<p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — ' .
            __('if the first attribute value is not available for the product the second will be used and so on untill it finds a value') . '<br>' .
            '<p>[prefix {attribute} suffix] or<br><p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — ' .
            __('if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone') . '.<br>';
    }

    /**
     * Return additional category comment
     *
     * @return string
     */
    public function getAdditionalCategoryComment()
    {
        return '<p>' . __('Additional variables available') . ': [category], [categories], [store_name], [website_name]<br>' .
            '<p><font color = "#ea7601">' . __('Note: The variables [category] and [categories] should be used when categories are added in product path only to avoid duplicates in meta tags') . '.</font>';
    }

    /**
     * Return comment for randomizer
     *
     * @return string
     */
    protected function getRandomizerComment()
    {
        return '<br><p>' . __('Randomizer feature is available. The construction like [Buy||Order||Purchase] will use a randomly picked word for each next item when applying a template.') . '<br>' .
            __('Also randomizers can be used within other template variables, ex: ') . '[for only||for {price}] .' .
            __('Number of randomizers blocks is not limited within the template.') . '<br>';
    }

    /**
     * Return example for meta title
     *
     * @return string
     */
    protected function getMetaTitleExample()
    {
        return '<p><b>' . __('Example') . '</b><p><p>[name][from||by {manufacturer|brand}][ ({color} color)][ for||for special {price}][ in {categories}] <p>'. __('will be transformed into') .
            '<br><p>HTC Touch Diamond by HTC (Black color) for € 517.50 in Cell Phones - Electronics';
    }

    /**
     * Return example for keywords
     *
     * @return string
     */
    protected function getKeywordsExample()
    {
        return '<p><b>' . __('Example') . '</b><p><p>[name][, {color} color][, {size} measurements||size][, {category}] <p>' . __('will be transformed into') . '<br>
                    <p>CN Clogs Beach/Garden Clog, Blue color, 10 size, Shoes';
    }

    /**
     * Return example for description
     *
     * @return string
     */
    protected function getDescriptionExample()
    {
        return '<p><b>' . __('Example') . '</b><p><p>[Buy||Order] [name][ by {manufacturer|brand}][ of {color} color][ for only||for {price}][ in {categories}] at[ {store_name},][ website_name]. [short_description] <p>' .
            __('will be transformed into') .
            '<br><p>Order HTC Touch Diamond by HTC of Black color for only € 517.50 in Cell Phones - Electronics at Digital Store, Digital-Store.com. HTC Touch Diamond signals a giant leap forward in combining hi-tech prowess with intuitive usability and exhilarating design';
    }

    /**
     * Return example for url
     *
     * @return string
     */
    protected function getUrlExample()
    {
        return '<p><b>' . __('Example') . '</b><p>[name][ by {manufacturer|brand}][ {color} color][ for {price}] <p>' . __('will be transformed into') .
            '<br><p>htc-touch-diamond-by-htc-black-color-for-517-50<br>';
    }

    /**
     * Return example for seo name
     *
     * @return string
     */
    protected function getSeoNameExample()
    {
        return '<p><b>' . __('Example') . '</b><p>[name][ by {manufacturer|brand}][ of {color} color][ for||for only {price}] <p>' . __('will be transformed into') .
            '<br><p>HTC Touch Diamond by HTC of Black color for only € 517.50<br>';
    }

    /**
     * @return string
     */
    public function getAdditionalGalleryComment()
    {
        return '<p>' . __('Additional variable available') . ': [image_position]<br>';
    }

    /**
     * Return example for gallery
     *
     * @return string
     */
    protected function getGalleryExample()
    {
        return '<p><b>' . __('Example') . '</b><p><p>[name][, {color} color][-{image_position}] <p>' . __('will be transformed into') . '<br>
                    <p>CN Clogs Beach/Garden Clog, Blue color-3';
    }
}
