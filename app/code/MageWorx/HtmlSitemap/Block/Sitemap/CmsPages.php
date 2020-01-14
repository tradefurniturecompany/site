<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Block\Sitemap;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use MageWorx\HtmlSitemap\Model\ResourceModel\Cms\PageFactory;

/**
 * CMS Pages sitemap block
 */
class CmsPages extends Template
{
    /**
     * @var \MageWorx\HtmlSitemap\Model\ResourceModel\Cms\PageFactory
     */
    protected $pageFactory;

    /**
     * @param \MageWorx\HtmlSitemap\Model\ResourceModel\Cms\PageFactory $pageFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        PageFactory $pageFactory,
        Context $context,
        array $data = []
    ) {
    
        $this->pageFactory = $pageFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve array of page items (\Magento\Framework\Object) or false
     *
     * @return array|bool
     * @see \MageWorx\HtmlSitemap\Model\ResourceModel\Cms\Page
     */
    public function getCollection()
    {
        return $this->pageFactory->create()->getCollection();
    }
}
