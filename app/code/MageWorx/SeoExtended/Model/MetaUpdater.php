<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Model;

use Magento\Framework\View\Page\Config as PageConfig;
use MageWorx\SeoExtended\Helper\Data as HelperData;

abstract class MetaUpdater implements \MageWorx\SeoExtended\Model\MetaUpdaterInterface
{
    /**
     *
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * MetaUpdater constructor.
     * @param HelperData $helperData
     * @param PageConfig $pageConfig
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        HelperData $helperData,
        PageConfig $pageConfig,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->helperData = $helperData;
        $this->pageConfig = $pageConfig;
        $this->coreRegistry = $coreRegistry;
        $this->storeManager = $storeManager;
        $this->request = $request;
    }

    /**
     * @param bool $onlyFilterReplace
     * @return mixed
     */
    abstract public function update($onlyFilterReplace = false);
}
