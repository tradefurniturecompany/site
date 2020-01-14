<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Helper;

/**
 * SEO Base URL helper
 *
 */
class Url extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Crop parametrs from URL
     *
     * @param string $url
     * @param array $cropParams
     * @return string
     */
    public function deleteUrlParametrs($url, array $cropParams)
    {
        $parseUrl = parse_url($url);

        if (empty($parseUrl['query'])) {
            return $url;
        }
        $params = '';
        parse_str(html_entity_decode($parseUrl['query']), $params);

        foreach ($cropParams as $cropName) {
            if (array_key_exists($cropName, $params)) {
                unset($params[$cropName]);
            }
        }

        $queryString = '';
        foreach ($params as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    if ($queryString == '') {
                        $queryString = '?' . $name . '=' . $val;
                    } else {
                        $queryString .= '&' . $name . '=' . $val;
                    }
                }
            } else {
                if ($queryString == '') {
                    $queryString = '?' . $name . '=' . $value;
                } else {
                    $queryString .= '&' . $name . '=' . $value;
                }
            }
        }

        $url = $parseUrl['scheme'] . '://' . $parseUrl['host'] . $parseUrl['path'] . $queryString;
        return $url;
    }

    /**
     * Crop all URL params except the set
     *
     * @param string $url
     * @param array $exceptParams
     * @return string
     */
    public function deleteUrlParametrsWithExcept($url, $exceptParams)
    {
        $parseUrl = parse_url($url);

        if (empty($parseUrl['query'])) {
            return $url;
        }
        $params = '';
        parse_str(html_entity_decode($parseUrl['query']), $params);

        foreach ($params as $paramName => $paramValue) {
            if (!in_array($paramName, $exceptParams)) {
                unset($params[$paramName]);
            }
        }

        $queryString = '';
        foreach ($params as $name => $value) {
            $queryString .= $queryString ? '&' : '?';
            $queryString .= $name . '=';
            $queryString .= is_array($value) ? implode(',', $value) : $value;
        }

        $url = $parseUrl['scheme'] . '://' . $parseUrl['host'] . $parseUrl['path'] . $queryString;
        return $url;
    }

    public function deleteAllParametrsFromUrl($url)
    {
        list($cropUrl) = explode('?', $url);
        return $cropUrl;
    }

    /**
     * Escape html entities in url
     *
     * @param string $url
     * @return string
     */
    public function escapeUrl($url)
    {
        return htmlspecialchars($url, ENT_COMPAT, 'UTF-8', false);
    }

    /**
     * Remove first page params
     *
     * @param string $url
     * @param string $pName
     * @return string
     */
    public function removeFirstPage($url, $pName = 'p')
    {
        $pageNum = $this->_request->getParam($pName);

        if ($pageNum == '1') {
            $url = str_replace(
                [   '?' . $pName . '=1&amp;',
                    '?' . $pName . '=1&',
                    '&amp;' . $pName . '=1&amp;',
                    '&' . $pName . '=1&',
                    '?' . $pName . '=1',
                    '&' . $pName . '=1',
                    '&amp;' . $pName . '=1'
                ],
                ['?', '?', '&amp;', '&'],
                $url
            );
        }

        return $url;
    }
}
