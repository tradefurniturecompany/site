<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\HtmlSitemap\Helper\Data as SitemapHelper;

/**
 * HTML sitemap controller
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \MageWorx\HtmlSitemap\Helper\Data
     */
    protected $sitemapHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\HtmlSitemap\Helper\Data $sitemapHelper
     */
    public function __construct(
        Context                      $context,
        PageFactory                  $resultPageFactory,
        StoreManagerInterface        $storeManager,
        SitemapHelper                $sitemapHelper
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory   = $resultPageFactory;
        $this->storeManager        = $storeManager;
        $this->sitemapHelper       = $sitemapHelper;
    }

    /**
     * View Sitemap action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->setMetaData();
        $this->_view->renderLayout();
    }

    /**
     * Set Sitemap Meta Data (title, desctiption, etc)
     *
     * @return void
     */
    public function setMetaData()
    {
        $resultPage = $this->resultPageFactory->create();

        $title       = $this->sitemapHelper->getTitle($this->storeManager->getStore()->getId());
        $keywords    = $this->sitemapHelper->getMetaKeywords($this->storeManager->getStore()->getId());
        $description = $this->sitemapHelper->getMetaDescription($this->storeManager->getStore()->getId());

        if ($title) {
            $resultPage->getConfig()->getTitle()->set($title);
        }
        if ($keywords) {
            $resultPage->getConfig()->setKeywords($keywords);
        }
        if ($description) {
            $resultPage->getConfig()->setDescription($description);
        }
    }
}
