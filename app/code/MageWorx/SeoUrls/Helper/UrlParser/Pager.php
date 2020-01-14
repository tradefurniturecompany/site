<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Helper\UrlParser;

use MageWorx\SeoUrls\Helper\UrlParserInterface;

class Pager implements UrlParserInterface
{
    const PARAMS_KEY = 'mw_pager';

    /**
     * @var \MageWorx\SeoUrls\Helper\Data $helperData
     */
    protected $helperData;

    /**
     * Pager constructor.
     * @param \MageWorx\SeoUrls\Helper\Data $helperData
     */
    public function __construct(\MageWorx\SeoUrls\Helper\Data $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($url, $pathInfo)
    {
        $pagerUrlFormat = $this->helperData->getPagerUrlFormat();
        $categorySuffix = $this->helperData->getSuffix();

        $pageNumberMask = \MageWorx\SeoUrls\Model\Source\PagerMask::PAGER_NUM_MASK;

        $urlPartsAroundPager = explode($pageNumberMask, $pagerUrlFormat);
        $quotedUrlPartsAroundPager = [];

        foreach ($urlPartsAroundPager as $part) {
            $quotedUrlPartsAroundPager[] = preg_quote($part, '/');
        }

        $pagerUrlFormatRegEx = implode('([0-9]+)', $quotedUrlPartsAroundPager);

        if (!preg_match('/' . $pagerUrlFormatRegEx . preg_quote($categorySuffix, '/') . '/', $url, $match)) {
            return false;
        }

        $pagerWithSuffix = $match[0];

        $params = [];
        if (!empty($match[1])) {
            $params = [self::PARAMS_KEY => [$this->helperData->getPagerVariableName() => $match[1]]];
        }
        $modUrl  = str_replace($pagerWithSuffix, $categorySuffix, $url);
        $modPath = str_replace($pagerWithSuffix, $categorySuffix, $pathInfo);

        return [
            'url'       => $modUrl,
            'path'      => $modPath,
            'params'    => $params
        ];
    }

    /**
     * @param array $params
     * @return array
     */
    public function convertParams($params)
    {
        if (!empty($params[self::PARAMS_KEY])) {
            $pagerParams = $params[self::PARAMS_KEY];
            unset($params[self::PARAMS_KEY]);
            $params = array_merge($params, $pagerParams);
        }
        return $params;
    }
}
