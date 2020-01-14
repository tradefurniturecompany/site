<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageWorx\XmlSitemap\Model\SitemapFactory as SitemapFactory;
use Magento\Framework\Registry;

abstract class Sitemap extends Action
{
    /**
     * Sitemap factory
     *
     * @var sitemapFactory
     */
    protected $sitemapFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Sitemap constructor.
     * @param Registry $registry
     * @param SitemapFactory $sitemapFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        SitemapFactory $sitemapFactory,
        Context $context
    ) {

        $this->coreRegistry   = $registry;
        $this->sitemapFactory = $sitemapFactory;
        parent::__construct($context);
    }

    /**
     * @param null $forceSitemapId
     * @return \MageWorx\XmlSitemap\Model\Sitemap
     */
    protected function initSitemap($forceSitemapId = null)
    {
        $sitemapId = is_null($forceSitemapId) ? $this->getRequest()->getParam('sitemap_id') : $forceSitemapId;

        $sitemap = $this->sitemapFactory->create();
        if ($sitemapId) {
            $sitemap->load($sitemapId);
        } else {
            $sitemap->setStoreId($this->getStoreId());
        }

        if (is_null($forceSitemapId)) {
            $this->coreRegistry->register('mageworx_xmlsitemap_sitemap', $sitemap);
        }

        return $sitemap;
    }

    /**
     *
     * @return int|null
     */
    protected function getStoreId()
    {
        $storeId = $this->getRequest()->getParam('store_id', -1);

        if ($storeId != -1) {
            return $storeId;
        }
        return null;
    }

    /**
     *
     * @param array $data
     * @return array
     */
    protected function filterData($data)
    {
        return $data;
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_XmlSitemap::sitemap');
    }
}