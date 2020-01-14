<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Helper\UrlBuilder;

use MageWorx\SeoAll\Helper\Layer as SeoAllHelperLayer;

class Layer
{
    /**
     * @var \MageWorx\SeoUrls\Helper\Data $helperData
     */
    protected $helperData;

    /**
     * @var \MageWorx\SeoUrls\Helper\Layer
     */
    protected $helperLayer;

    /**
     * @var \MageWorx\SeoUrls\Helper\Url
     */
    protected $helperUrl;

    /**
     * @var SeoAllHelperLayer
     */
    protected $helperLayerAll;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Layer constructor.
     * @param \MageWorx\SeoUrls\Helper\Data $helperData
     * @param \MageWorx\SeoUrls\Helper\Layer $helperLayer
     * @param \MageWorx\SeoUrls\Helper\Url $helperUrl
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \MageWorx\SeoUrls\Helper\Data $helperData,
        \MageWorx\SeoUrls\Helper\Layer $helperLayer,
        \MageWorx\SeoUrls\Helper\Url $helperUrl,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder,
        SeoAllHelperLayer $helperLayerAll,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->helperData     = $helperData;
        $this->helperLayer    = $helperLayer;
        $this->helperUrl      = $helperUrl;
        $this->request        = $request;
        $this->urlBuilder     = $urlBuilder;
        $this->helperLayerAll = $helperLayerAll;
        $this->storeManager   = $storeManager;
    }

    /**
     * @param $params
     * @return string
     */
    public function getLayerFilterUrl($params)
    {
        $hideAttributes = $this->helperData->getIsHideAttributes();
        $queryParams = $this->request->getParams();

        $attr = $this->helperLayer->getFilterableAttributes();

        $multipleValueSeparator = $this->helperLayerAll->getMultipleValueSeparator();

        //restore seo values for loaded attributes
        foreach ($queryParams as $name => $value) {
            if ($name == 'price') {
                continue;
            }
            
            $friendlyValue = '';

            if (is_array($value)) {
                $options = $value;
            } else {
                $options = explode($multipleValueSeparator, $value);
            }

            foreach ($options as $option) {
                if (!empty($attr[$name]['options'][$option])) {
                    $separatePrefix = ($friendlyValue === '') ? '' : $multipleValueSeparator;
                    $friendlyValue .= $separatePrefix . $this->helperLayer->formatUrlKey($attr[$name]['options'][$option]);
                }
            }
            $queryParams[$name] = $friendlyValue;
        }

        if (isset($queryParams['price']) && is_array($queryParams['price'])) {
            $queryParams['price'] = join(' ', $queryParams['price']);
        }

        if (isset($queryParams['price']) && strpos($queryParams['price'], '-') !== false) {
            $multipliers = explode('-', $queryParams['price']);
            $priceFrom = floatval($multipliers[0]);
            $priceTo = (!$multipliers[1] ? '' : floatval($multipliers[1]));
            $queryParams['price'] = $priceFrom . '-' . $priceTo;
        }

        foreach ($params['_query'] as $param => $value) {
            $queryParams[$param] = $value;
        }

        $queryParams = array_filter($queryParams);
        $attr = $this->helperLayer->getFilterableAttributes();

        $layerParams = [];

        array_walk_recursive(
            $queryParams,
            function (&$item) {
                $item = urldecode($item);
            }
        );

        foreach ($queryParams as $param => $value) {

            if ($param == 'cat' || isset($attr[$param])) {
                switch ($hideAttributes) {
                    case true:
                        /** @todo hide attributes */
                        break;
                    default:
                        if ($param == 'cat') {
                            $key = 0;
                            $result = $this->helperLayer->formatUrlKey($value);
                        } else {
                            $key = $param;
                            $separator = \MageWorx\SeoUrls\Helper\Data::LAYER_FILTERS_SEPARATOR;
                            $result = $this->helperLayer->formatUrlKey($param) . $separator;

                            if ($attr[$param]['type'] == 'decimal') {
                                $result .= is_array($value) ? implode('-', $value) : $value;
                            } else {
                                $result .= $this->helperLayer->formatUrlKey($value);
                            }
                        }

                        $layerParams[$key] = $result;

                        break;
                }
                $params['_query'][$param] = null;
            }
        }

        $layer = null;
        if (!empty($layerParams)) {
            uksort($layerParams, 'strcmp');
            $layer = implode('/', $layerParams);
        }
        $url = $this->urlBuilder->getUrl('*/*/*', $params);

        if (!$layer) {
            return $url;
        }

        $urlParts = explode('?', $url, 2);
        $suffix = $this->helperData->getSuffix();
        $url = $this->helperUrl->removeSuffix($urlParts[0], $suffix);

        $navIdentifier = $this->helperData->getSeoUrlIdentifier();
        $resultUrl = $url . '/' . $navIdentifier . '/' . $layer . $suffix . (isset($urlParts[1]) ? '?' . $urlParts[1] : '');

        return $resultUrl;
    }
}
