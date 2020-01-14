<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Model\ResourceModel\Cms;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;
use MageWorx\SeoAll\Helper\LinkFieldResolver;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\HtmlSitemap\Helper\Data as SitemapHelper;
use MageWorx\HtmlSitemap\Helper\StoreUrl as StoreUrlHelper;

/**
 * HTML Sitemap cms page collection model
 */
class Page extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\HtmlSitemap\Helper\Data
     */
    protected $sitemapHelper;

    /**
     * @var \MageWorx\HtmlSitemap\Helper\StoreUrl
     */
    protected $storeUrlHelper;

    /**
     * @var \MageWorx\SeoAll\Helper\LinkFieldResolver
     */
    protected $linkFieldResolver;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\HtmlSitemap\Helper\Data $sitemapHelper
     * @param \MageWorx\HtmlSitemap\Helper\StoreUrl $storeUrlHelper
     * @param \MageWorx\SeoAll\Helper\LinkFieldResolver $linkFieldResolver
     */
    public function __construct(
        Context $context,
        LinkFieldResolver $linkFieldResolver,
        StoreManagerInterface $storeManager,
        SitemapHelper $sitemapHelper,
        StoreUrlHelper $storeUrlHelper
    ) {

        parent::__construct($context);
        $this->storeManager  = $storeManager;
        $this->sitemapHelper = $sitemapHelper;
        $this->storeUrlHelper = $storeUrlHelper;
        $this->linkFieldResolver = $linkFieldResolver;
    }

    /**
     * Init resource model (catalog/category)
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cms_page', 'page_id');
    }

    /**
     * Retrieve cms page collection array
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $storeId
     * @return array|bool
     */
    public function getCollection($storeId = null)
    {
        $store = $this->storeManager->getStore($storeId);
        if (!$store) {
            return false;
        }
        $linkField = $this->linkFieldResolver->getLinkField(PageInterface::class, 'page_id');

        $pages = [];

        $select = $this->getConnection()->select()->from(
            ['main_table' => $this->getMainTable()],
            [$this->getIdFieldName(), 'url' => 'identifier', 'title']
        )->join(
            ['store_table' => $this->getTable('cms_page_store')],
            "main_table.{$linkField} = store_table.$linkField",
            []
        )->where(
            'main_table.is_active = 1'
        )->where(
            'main_table.identifier != ?',
            \Magento\Cms\Model\Page::NOROUTE_PAGE_ID
        )->where(
            'main_table.in_html_sitemap = ?',
            1
        )->where(
            'store_table.store_id IN(?)',
            [0, $store->getId()]
        );

        $query = $this->getConnection()->query($select);
        while ($row   = $query->fetch()) {
            $page                  = $this->prepareObject($row, $store->getId());
            $pages[$page->getId()] = $page;
        }

        return $pages;
    }

    /**
     * Prepare page object
     *
     * @param array $data
     * @return \Magento\Framework\Object
     */
    protected function prepareObject(array $data, $storeId)
    {
        $object = new \Magento\Framework\DataObject();
        $object->setId($data[$this->getIdFieldName()]);
        $object->setTitle($data['title']);

        if (!empty($data['url'])) {
            $homePageId = null;
            $homeIdentifier = $this->sitemapHelper->getHomeIdentifier();
            if (strpos($homeIdentifier, '|') !== false) {
                list($homeIdentifier, $homePageId) = explode('|', $homeIdentifier);
            }
            if ($homeIdentifier == $data['url']) {
                $data['url'] = '';
                $object->setUrl($this->trailingSlash($this->storeUrlHelper->getUrl($data['url'], $storeId), true));
            } else {
                $object->setUrl($this->trailingSlash($this->storeUrlHelper->getUrl($data['url'], $storeId)));
            }
        }
        return $object;
    }

    /**
     * Crop or add trailing slash
     *
     * @param string $url
     * @param bool $isHomePage
     * @return string
     */
    protected function trailingSlash($url, $isHomePage = false)
    {
        if ($isHomePage) {
            $trailingSlash = $this->sitemapHelper->getTrailingSlashForHomePage();
        } else {
            $trailingSlash = $this->sitemapHelper->getTrailingSlash();
        }

        if ($trailingSlash == \MageWorx\HtmlSitemap\Model\Source\AddCrop::TRAILING_SLASH_ADD) {
            $url        = rtrim($url);
            $extensions = ['rss', 'html', 'htm', 'xml', 'php'];
            if (substr($url, -1) != '/' && !in_array(substr(strrchr($url, '.'), 1), $extensions)) {
                $url.= '/';
            }
        } elseif ($trailingSlash == \MageWorx\HtmlSitemap\Model\Source\AddCrop::TRAILING_SLASH_CROP) {
            $url = rtrim(rtrim($url), '/');
        }

        return $url;
    }
}
