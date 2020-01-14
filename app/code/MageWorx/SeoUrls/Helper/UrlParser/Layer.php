<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Helper\UrlParser;

use MageWorx\SeoUrls\Helper\UrlParserInterface;

class Layer implements UrlParserInterface
{
    const PARAMS_KEY = 'mw_ln';

    /**
     * @var \MageWorx\SeoUrls\Helper\Data $helperData
     */
    protected $helperData;

    /**
     * @var \MageWorx\SeoUrls\Helper\Layer
     */
    protected $helperLayer;

    /**
     * Layer constructor.
     * @param \MageWorx\SeoUrls\Helper\Data $helperData
     * @param \MageWorx\SeoUrls\Helper\Layer $helperLayer
     */
    public function __construct(
        \MageWorx\SeoUrls\Helper\Data $helperData,
        \MageWorx\SeoUrls\Helper\Layer $helperLayer
    ) {
        $this->helperData  = $helperData;
        $this->helperLayer = $helperLayer;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($url, $pathInfo)
    {
        $layeredIdentifier = $this->helperData->getSeoUrlIdentifier();

        if (!$layeredIdentifier || strpos($pathInfo, $layeredIdentifier) === false) {
            return false;
        }

        $categorySuffix = $this->helperData->getSuffix();

        if ($categorySuffix) {
            $layerUrlFormatRegEx = preg_quote('/' . $layeredIdentifier . '/', '/') . '(.+)' . preg_quote($categorySuffix, '/');

            if (!preg_match('/' . $layerUrlFormatRegEx . '/', $url, $match)) {
                return false;
            }

            $layerWithSuffix = $match[0];
            $layerParams     = $this->buildLayeredParams($match[1]);

            $modUrl  = str_replace($layerWithSuffix, $categorySuffix, $url);
            $modPath = str_replace($layerWithSuffix, $categorySuffix, $pathInfo);
        } else {
            $layerUrlFormatRegEx = preg_quote('/' . $layeredIdentifier . '/', '/') . '(.+)';

            $urlParts = explode('?', $url);

            if (!preg_match('/' . $layerUrlFormatRegEx . '/', $urlParts[0], $match)) {
                return false;
            }

            $layerPart   = $match[0];
            $layerParams = $this->buildLayeredParams($match[1]);

            $modUrl  = str_replace($layerPart, '', $url);
            if (!empty($urlParts[1])) {
                $modUrl  = implode('?', [$modUrl, $urlParts[1]]);
            }

            $modPath = str_replace($layerPart, '', $pathInfo);
        }

        return [
            'url'       => $modUrl,
            'path'      => $modPath,
            'params'    => [self::PARAMS_KEY => $layerParams]
        ];
    }

    /**
     * @param string $paramsAsString
     * @return array
     */
    protected function buildLayeredParams($paramsAsString)
    {
        $params = explode('/', $paramsAsString);

        $modParams = [];

        foreach ($params as $key => $param) {
            $separator = \MageWorx\SeoUrls\Helper\Data::LAYER_FILTERS_SEPARATOR;
            if (strpos($param, $separator) !== false) {
                list($attributeName, $attributeValue) = explode($separator, $param);
                if ($attributeName && $attributeValue) {
                    $key   = $attributeName;
                    $param = $attributeValue;
                }
            }
            $modParams[$key] = $param;
        }
        return $modParams;
    }

    /**
     * @param array $params
     * @return array
     */
    public function convertParams($params)
    {
        if (!empty($params[self::PARAMS_KEY])) {
            $filterParsedParams = $this->helperLayer->parseLayeredParams($params[self::PARAMS_KEY]);
            unset($params[self::PARAMS_KEY]);
            $params = array_merge($params, $filterParsedParams);
        }
        return $params;
    }
}