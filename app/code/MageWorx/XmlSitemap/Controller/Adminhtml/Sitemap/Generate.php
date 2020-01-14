<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageWorx\XmlSitemap\Controller\Adminhtml\Sitemap;

use MageWorx\XmlSitemap\Controller\Adminhtml\Sitemap as SitemapController;

class Generate extends SitemapController
{
    /**
     * Generate sitemap
     *
     * @return void
     */
    public function execute()
    {
        // init and load sitemap model
        $id = $this->getRequest()->getParam('sitemap_id');
        $sitemap = $this->_objectManager->create('MageWorx\XmlSitemap\Model\Sitemap');
        /* @var $sitemap \Magento\Sitemap\Model\Sitemap */
        $sitemap->load($id);
        // if sitemap record exists
        if ($sitemap->getSitemapId()) {
            try {
                $sitemap->generateXml();

                $this->messageManager->addSuccess(
                    __('The sitemap "%1" has been generated.', $sitemap->getSitemapFilename())
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t generate the sitemap right now.'));
            }
        } else {
            $this->messageManager->addError(__('We can\'t find a sitemap to generate.'));
        }

        // go to grid
        $this->_redirect('mageworx_xmlsitemap/*/');
    }
}
