<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoMarkup\Block\Adminhtml\Config\Field;

class ConditionEnabled extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * {@inheritdoc}
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $link = 'https://schema.org/weight';

        $comment = __(
            "Map any existing product attribute to the Schema.org %link.",
            ['link' => '<a target="_blank" href="' . $link . '">' . __('priority of the categories') . '</a>']
        );

        $linkToNew         = 'https://schema.org/NewCondition';
        $linkToUsed        = 'https://schema.org/UsedCondition';
        $linkToRefurbished = 'https://schema.org/RefurbishedCondition';
        $linkToDamaged     = 'https://schema.org/DamagedCondition';

        $comment .= ' ' . __(
            "Assign the current product attribute options to the Schema.org condition options such as %new, %used, %refurbished or %damaged.",
            [
                'new'         => '<a target="_blank" href="' . $linkToNew . '">' . __('New') . '</a>',
                'used'        => '<a target="_blank" href="' . $linkToUsed . '">' . __('Used') . '</a>',
                'refurbished' => '<a target="_blank" href="' . $linkToRefurbished . '">' . __('Refurbished') . '</a>',
                'damaged'     => '<a target="_blank" href="' . $linkToDamaged . '">' . __('Damaged') . '</a>'
            ]
        );

        $element->setComment($comment);

        return parent::render($element);
    }
}