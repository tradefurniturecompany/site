<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Robots;

use MageWorx\SeoBase\Helper\Data as HelperData;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Layout;

/**
 * SEO Base CMS page robots model
 */
class Page extends \MageWorx\SeoBase\Model\Robots
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\View\Layout
     */
    protected $layout;

    /**
     *
     * @param HelperData $helperData
     * @param Registry $registry
     * @param RequestInterface $request
     * @param UrlInterface $url
     * @param Layout $layout
     */
    public function __construct(
        HelperData $helperData,
        Registry $registry,
        RequestInterface $request,
        UrlInterface $url,
        Layout $layout,
        $fullActionName
    ) {
    
        $this->layout   = $layout;
        $this->registry = $registry;
        $this->data     = $layout;
        parent::__construct($helperData, $request, $url, $fullActionName);
    }

    /**
     * Retrieve final robots
     *
     * @return string
     */
    public function getRobots()
    {
        $metaRobots = $this->getPageRobots();
        return $metaRobots ? $metaRobots : $this->getRobotsBySettings();
    }

    /**
     * Retrieve robots from CMS page data
     *
     * @return string|null
     */
    protected function getPageRobots()
    {
        $block = $this->layout->getBlock('cms_page');
        if (is_object($block)) {
            $page = $block->getPage();
            if (is_object($page) && $page->getMetaRobots()) {
                return $page->getMetaRobots();
            }
        }
        return null;
    }
}
