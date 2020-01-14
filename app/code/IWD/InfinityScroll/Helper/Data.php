<?php

namespace IWD\InfinityScroll\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 * @package IWD\OrderManager\Helper
 */
class Data extends AbstractHelper
{
    /**
     * XPath: extension enable
     */
    const ENABLED = 'infinity_scroll/general/enable';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::ENABLED) ? true : false;
    }
}
