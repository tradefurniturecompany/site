<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Robots;

use MageWorx\SeoBase\Helper\Data as HelperData;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

/**
 * SEO Base product robots model
 */
class Product extends \MageWorx\SeoBase\Model\Robots
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     *
     * @param HelperData $helperData
     * @param Registry $registry
     * @param RequestInterface $request
     * @param UrlInterface $url
     */
    public function __construct(
        HelperData $helperData,
        Registry $registry,
        RequestInterface $request,
        UrlInterface $url,
        $fullActionName
    ) {
    
        $this->registry = $registry;
        parent::__construct($helperData, $request, $url, $fullActionName);
    }

    /**
     * Retrieve final robots
     *
     * @return string
     */
    public function getRobots()
    {
        $metaRobots = $this->getProductRobots();
        return $metaRobots ? $metaRobots : $this->getRobotsBySettings();
    }

    /**
     * Retrieve robots from product atttibute
     *
     * @return string|null
     */
    protected function getProductRobots()
    {
        $product = $this->registry->registry('current_product');
        if (is_object($product) && $product->getMetaRobots()) {
            return $product->getMetaRobots();
        }
        return null;
    }
}
