<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoExtended\Block\Adminhtml\Config\Field;

class EnableCategoryFilterFunctionality extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * {@inheritdoc}
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $link = $this->getUrl('mageworx_seoextended/categoryfilter');

        $comment = __(
            "This setting enables the Category Filter functionality. To add, edit or delete the category filters, click %link.",
            ['link' => '<a target="_blank" href="' . $link . '">' . __('here') . '</a>']
        );

        $element->setComment($comment);

        return parent::render($element);
    }
}