<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Helper;

/**
 * SEO Urls URL helper
 */
class SeoUrlParser extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \MageWorx\SeoUrls\Helper\UrlParserInterface[]
     */
    protected $parsers;

    /**
     * Url constructor.
     * @param \MageWorx\SeoUrls\Helper\UrlParserInterface[] $parsers
     */
    public function __construct($parsers = [])
    {
        ksort($parsers);
        $this->parsers = $parsers;
    }

    /**
     * @param string $url
     * @param string $path
     * @param bool $isRebuildParamsNow
     * @return array
     */
    public function getConvertedUrlData($url, $path, $isRebuildParamsNow = false)
    {
        $params = [];

        foreach ($this->parsers as $parser) {
            $result = $parser->parse($url, $path);

            if ($result) {
                $url      = $result['url'];
                $path     = $result['path'];
                $params   = array_merge($params, $result['params']);
            }
        }

        if ($isRebuildParamsNow) {
            $params = $this->rebuildParams($params);
        }
        return ['url' => $url, 'path' => $path, 'params' => $params];
    }

    /**
     * @param $params
     * @return array
     */
    public function rebuildParams($params)
    {
        foreach ($this->parsers as $parser) {
            $params = $parser->convertParams($params);
        }

        return $params;
    }
}
