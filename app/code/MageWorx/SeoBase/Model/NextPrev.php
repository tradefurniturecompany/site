<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model;

use MageWorx\SeoBase\Helper\Data as HelperData;
use MageWorx\SeoBase\Helper\Url as HelperUrl;

abstract class NextPrev implements \MageWorx\SeoBase\Model\NextPrevInterface
{
    /**
     * @return string URL
     */
    abstract public function getNextUrl();

    /**
     * @return string URL
     */
    abstract public function getPrevUrl();

    /**
     * @var \MageWorx\SeoBase\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\SeoBase\Helper\Url
     */
    protected $helperUrl;

    public function __construct(
        HelperData $helperData,
        HelperUrl  $helperUrl
    ) {
        $this->helperData = $helperData;
        $this->helperUrl  = $helperUrl;
    }

    /**
     * Remove pager param from URL is page num = 1
     *
     * @param string $url
     * @raram string $pageVarName
     * @return string
     */
    protected function removeFirstPage($url, $pageVarName)
    {
        return $this->helperUrl->removeFirstPage($url, $pageVarName);
    }
}
