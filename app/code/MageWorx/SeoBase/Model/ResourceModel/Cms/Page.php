<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoBase\Model\ResourceModel\Cms;

use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * SEO Base cms page collection model
 */
class Page extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \MageWorx\SeoBase\Helper\Data
     */
    protected $helperData;

    /**
     *
     * @var \MageWorx\SeoBase\Helper\StoreUrl
     */
    protected $helperStoreUrl;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $cmsFactory;

    /**
     * @var \MageWorx\SeoAll\Helper\LinkFieldResolver
     */
    protected $linkFieldResolver;

    /**
     *
     * @param \MageWorx\SeoBase\Helper\Data $helperData
     * @param \MageWorx\SeoBase\Helper\StoreUrl $helperStoreUrl
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Context $context
     */
    public function __construct(
        \MageWorx\SeoBase\Helper\Data $helperData,
        \MageWorx\SeoBase\Helper\StoreUrl $helperStoreUrl,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\PageFactory $cmsFactory,
        \MageWorx\SeoAll\Helper\LinkFieldResolver $linkFieldResolver,
        Context $context
    ) {
        parent::__construct($context);

        $this->helperData     = $helperData;
        $this->helperStoreUrl = $helperStoreUrl;
        $this->storeManager   = $storeManager;
        $this->cmsFactory     = $cmsFactory;
        $this->linkFieldResolver = $linkFieldResolver;
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cms_page', 'page_id');
    }
}
