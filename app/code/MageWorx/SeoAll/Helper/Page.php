<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;

class Page extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Category constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Context $context
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Context $context
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param string $urlPart
     * @param int|null $pageId
     * @param int|null $storeId
     * @return bool
     */
    public function getIsHomePage($urlPart, $pageId = null, $storeId = null)
    {
        $homeIdentifier = $this->getHomeIdentifier($storeId);

        if (strpos($homeIdentifier, '|') !== false) {
            list($homeIdentifier, $homePageId) = explode('|', $homeIdentifier);
        }

        if (trim($homeIdentifier, '/') == trim($urlPart, '/')) {

            if ($pageId && !empty($homePageId) && $pageId != $homePageId) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Retrieve home page identifier
     *
     * @param int|null $storeId
     * @return string
     */
    public function getHomeIdentifier($storeId = null)
    {
        return $this->scopeConfig->getValue(
            \Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}