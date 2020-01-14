<?php
/**
 * Copyright ©  MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Info\Model\System;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Extensions extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * @var \MageWorx\Info\Block\Adminhtml\Extensions
     */
    protected $extensionBlock;

    /**
     * Extensions constructor.
     *
     * @param \MageWorx\Info\Block\Adminhtml\Extensions $extensionBlock
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\View\Helper\Js $jsHelper
     * @param array $data
     */
    public function __construct(
        \MageWorx\Info\Block\Adminhtml\Extensions $extensionBlock,
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\View\Helper\Js $jsHelper,
        array $data = []
    ) {
        $this->extensionBlock = $extensionBlock;
        parent::__construct($context, $authSession, $jsHelper, $data);
    }

    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        return $this->extensionBlock->toHtml();
    }
}
