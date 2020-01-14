<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Controller\Adminhtml\Sitemap;

use MageWorx\XmlSitemap\Controller\Adminhtml\Sitemap as SitemapController;
use Magento\Backend\App\Action\Context;
use MageWorx\XmlSitemap\Model\SitemapFactory as SitemapFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Create extends SitemapController
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Create constructor.
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param SitemapFactory $sitemapFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        SitemapFactory $sitemapFactory,
        Context $context
    ) {

        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($registry, $sitemapFactory, $context);
    }

    /**
     * Create product sitemap
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $sitemapId = $this->getRequest()->getParam('sitemap_id');
        /** @var \MageWorx\XmlSitemap\Model\Sitemap $sitemap */
        $sitemap = $this->initSitemap();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageWorx_XmlSitemap::sitemap');
        $resultPage->getConfig()->getTitle()->set((__('Sitemap')));
        if ($sitemapId) {
            $sitemap->load($sitemapId);
            if (!$sitemap->getSitemapId()) {
                $this->messageManager->addError(__('The sitemap no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath(
                    'mageworx_xmlsitemap/*/edit',
                    [
                        'sitemap_id' => $sitemap->getSitemapId(),
                        '_current' => true
                    ]
                );
                return $resultRedirect;
            }
        }

        $title = $sitemap->getSitemapId() ? $sitemap->getName() : __('New Sitemap');
        $data  = $this->_session->getData('mageworx_xmlsitemap_sitemap', true);

        $resultPage->getConfig()->getTitle()->append($title);
        if (!empty($data)) {
            $sitemap->setData($data);
        }
        return $resultPage;
    }
}
