<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Helper;

use Magento\Store\Model\ScopeInterface;
use MageWorx\SeoUrls\Model\Source\PagerMask;

/**
 * SEO Urls helper
 */
class Url extends \Magento\Framework\Url\Helper\Data
{
    /**
     * @param string $url
     * @param string $suffix
     * @return string
     */
    public function removeSuffix($url, $suffix)
    {
        if (strlen($suffix)) {
            $pos = strrpos($url, $suffix);
            if ($pos !== false && $pos == strlen($url) - strlen($suffix)) {
                $url = substr($url, 0, $pos);
            }
        }
        return $url;
    }

    /**
     * @param string $url
     * @param string $suffix
     * @return string
     */
    public function addSuffix($url, $suffix)
    {
        $url .= $suffix;
        return $url;
    }
}
