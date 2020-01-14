<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBreadcrumbs\Block\Adminhtml\Config\Field;

class UseCategoryPriority extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * {@inheritdoc}
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $link = $this->getUrl('mageworx_seobreadcrumbs/category/');

        $comment = __(
            "If enabled, the system will use the %link to choose the breadcrumbs",
            ['link' => '<a target="_blank" href="' . $link . '">' . __('priority of the categories') . '</a>']
        );

        $element->setComment($comment);

        return parent::render($element);
    }
}