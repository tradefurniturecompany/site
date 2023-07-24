<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Encryption;

/**
 * @api
 * @since 100.0.2
 */
class UrlCoder
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(\Magento\Framework\UrlInterface $url)
    {
        $this->_url = $url;
    }

    /**
     * base64_encode() for URLs encoding
     *
     * @param    string $url
     * @return   string
     */
    public function encode($url)
    {
        return strtr(base64_encode($url), '+/=', '-_,');
    }

    /**
     *  base64_decode() for URLs decoding
     *
     * @param    string $url
     * @return   string
     */
    public function decode($url)
    {
		# 2023-07-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# «strtr() expects parameter 1 to be string, array given
		# in vendor/magento/framework/Encryption/UrlCoder.php on line 47»:
		# https://github.com/tradefurniturecompany/site/issues/267
		if (is_array($url)) {
			df_log(['issue' => 'https://github.com/tradefurniturecompany/site/issues/267', 'url' => $url]);
		}
        return $this->_url->sessionUrlVar(base64_decode(strtr($url, '-_,', '+/=')));
    }
}
