<?php
/**
 * Copyright ©  MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Info\Observer;

use Magento\Framework\Event\ObserverInterface;

class GetMageWorxExtensionList implements ObserverInterface
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $backendSession;

    /**
     * @var \MageWorx\Info\Helper\Data
     */
    protected $helper;

    /**
     * GetMageWorxOffers constructor.
     *
     * @param \MageWorx\Info\Helper\Data $helper
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     */
    public function __construct(
        \MageWorx\Info\Helper\Data $helper,
        \Magento\Backend\Model\Auth\Session $backendSession
    ) {
        $this->helper         = $helper;
        $this->backendSession = $backendSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->backendSession->isLoggedIn() && $this->helper->isExtensionInfoAutoloadEnabled()) {
            $this->helper->checkExtensionListUpdate();
        }
    }
}