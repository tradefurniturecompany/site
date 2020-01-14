<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Block\Adminhtml\Config\Field;

class UseProductSeoName extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * {@inheritdoc}
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $link = $this->getUrl('catalog/product/');

        $comment = __(
            "We add the special <b>SEO Name</b> attribute that can be used instead of the real Product name (populates the H1 tag)."
        );

        $comment .= ' ' . __(
            "Choose the desired Product on the %productGridLink and click the Search Engine Optimization tab.",
            ['productGridLink' => '<a target="_blank" href="' . $link . '">' . __('Products Grid') . '</a>']
        );

        $comment .= ' ' . __(
            "SEO Name allows you keeping the product names short and relevant but optimize the H1 tag on the product pages at the same time."
        );

        $element->setComment($comment);

        return parent::render($element);
    }
}