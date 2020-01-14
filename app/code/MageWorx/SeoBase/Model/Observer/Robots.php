<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Observer;

use Magento\Framework\View\Page\Config;
use MageWorx\SeoBase\Model\RobotsFactory;

/**
 * Observer class for robots
 */
class Robots implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \MageWorx\SeoBase\Model\RobotsFactory
     */
    protected $robotsFactory;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * Robots constructor.
     * @param Config $pageConfig
     * @param RobotsFactory $robotsFactory
     */
    public function __construct(
        Config    $pageConfig,
        RobotsFactory $robotsFactory
    ) {
    
        $this->pageConfig    = $pageConfig;
        $this->robotsFactory = $robotsFactory;
    }

    /**
     * Set robots to page config
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //layout_generate_blocks_before
        $fullActionName = $observer->getFullActionName();
        $arguments      = ['layout' => $observer->getLayout(), 'fullActionName' => $fullActionName];
        $robotsInstance = $this->robotsFactory->create($fullActionName, $arguments);
        $robots         = $robotsInstance->getRobots();

        if ($robots) {
            $this->pageConfig->setRobots($robots);
        }
    }
}
