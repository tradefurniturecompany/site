<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Observer\Layout;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class Handle implements ObserverInterface
{
    /**
     * @var \Amasty\MegaMenu\Helper\Config
     */
    private $configHelper;

    public function __construct(\Amasty\MegaMenu\Helper\Config $configHelper)
    {
        $this->configHelper = $configHelper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\LayoutInterface $layout */
        $layout = $observer->getEvent()->getLayout();

        if ($layout && $this->configHelper->isEnabled()) {
            $layout->getUpdate()->addHandle('am_mega_menu_layout');
        }
    }
}
