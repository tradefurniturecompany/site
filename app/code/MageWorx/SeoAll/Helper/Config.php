<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoAll\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * @param int|null $store
     * @return string
     */
    public function useCategoriesPathInProductUrl($store = null)
    {
        return (bool) $this->scopeConfig->getValue(
            \Magento\Catalog\Helper\Product::XML_PATH_PRODUCT_URL_USE_CATEGORY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}