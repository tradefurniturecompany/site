<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Controller\Adminhtml\Sitemap;

use MageWorx\XmlSitemap\Controller\Adminhtml\Sitemap as SitemapController;

class Delete extends SitemapController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('sitemap_id');
        if ($id) {
            try {
                /** @var \MageWorx\XmlSitemap\Model\Sitemap $sitemap */
                $sitemap = $this->sitemapFactory->create();
                $sitemap->load($id);
                $filename = $sitemap->getSitemapFilename();
                //@todo delete all sitemap files
                $sitemap->delete();
                $this->messageManager->addSuccess(__('The "%1" sitemap has been deleted.', $filename));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setPath('mageworx_xmlsitemap/*/edit', ['sitemap_id' => $id]);
            }
            $resultRedirect->setPath(
                'mageworx_xmlsitemap/*/',
                [
                    'sitemap_id' => $sitemap->getSitemapId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
        $this->messageManager->addError(__('We can\'t find a sitemap to delete.'));
        $resultRedirect->setPath('mageworx_xmlsitemap/*/');
        return $resultRedirect;
    }
}