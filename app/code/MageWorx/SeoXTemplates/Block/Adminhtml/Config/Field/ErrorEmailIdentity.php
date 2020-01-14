<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Block\Adminhtml\Config\Field;

class ErrorEmailIdentity extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * {@inheritdoc}
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $link = $this->getUrl('adminhtml/system_config/edit/section/trans_email/');

        $comment =  __(
            "Choose the %emailSenderLink",
            ['emailSenderLink' => '<a target="_blank" href="' . $link . '">' . __('Email Sender') . '</a>']
        );

        $element->setComment($comment);

        return parent::render($element);
    }
}