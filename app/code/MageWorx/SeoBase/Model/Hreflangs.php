<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model;

use MageWorx\SeoBase\Helper\Data as HelperData;
use MageWorx\SeoBase\Helper\Url  as HelperUrl;

abstract class Hreflangs implements \MageWorx\SeoBase\Model\HreflangsInterface
{
    /**
     * @var \MageWorx\SeoBase\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\SeoBase\Helper\Url
     */
    protected $helperUrl;

    /**
     * @var string
     */
    protected $fullActionName;


    /**
     * Retrieve hreflang URL list:
     * [
     *      (int)$storeId => (string)$hreflangUrl,
     *      ...
     * ]
     *
     * @return array
     */
    abstract public function getHreflangUrls();

    /**
     *
     * @param HelperData $helperData
     * @param HelperUrl $helperUrl
     * @param string $fullActionName
     */
    public function __construct(
        HelperData $helperData,
        HelperUrl  $helperUrl,
        $fullActionName
    ) {
        $this->helperData     = $helperData;
        $this->helperUrl      = $helperUrl;
        $this->fullActionName = $fullActionName;
    }

    /**
     * Check if cancel adding hreflangs URL by config setting
     *
     * @return bool
     */
    protected function isCancelHreflangs()
    {
        return ($this->helperData->isHreflangsEnabled() == false);
    }
}
