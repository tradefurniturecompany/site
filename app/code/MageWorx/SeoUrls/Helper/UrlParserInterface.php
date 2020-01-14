<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Helper;

/**
 * @api
 */
interface UrlParserInterface
{
    /**
     * @param string $url
     * @param string $pathInfo
     * @return array
     */
    public function parse($url, $pathInfo);

    /**
     * @param $params
     * @return array
     */
    public function convertParams($params);
}
