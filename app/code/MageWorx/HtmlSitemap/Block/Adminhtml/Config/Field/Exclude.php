<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Block\Adminhtml\Config\Field;

class Exclude extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * {@inheritdoc}
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $linkToProduct  = 'https://support.mageworx.com/images/manuals/sitemap/image12.png';
        $linkToCategory = 'https://support.mageworx.com/images/manuals/seosuite/image28.png';
        $linkToPage     = 'https://support.mageworx.com/images/manuals/seosuite/image11.png';

        $comment = __(
            "You can exclude any specific CMS, Category, and Product pages from HTML sitemap on the corresponding grids, or on their Edit pages (%linkProduct, %linkCategory, %linkPage).",
            [
                'linkProduct'  => '<a target="_blank" href="' . $linkToProduct . '">' . __('Product') . '</a>',
                'linkCategory' => '<a target="_blank" href="' . $linkToCategory . '">' . __('Category') . '</a>',
                'linkPage'     => '<a target="_blank" href="' . $linkToPage . '">' . __('Page') . '</a>',
            ]
        );

        $element->setComment($comment);

        return parent::render($element);
    }
}