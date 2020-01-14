<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model;

use MageWorx\SeoBase\Helper\Data as HelperData;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

abstract class Robots implements \MageWorx\SeoBase\Model\RobotsInterface
{
    abstract public function getRobots();

    /**
     * @var \MageWorx\SeoBase\Helper\Data
     */
    protected $helperData;

    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var string
     */
    protected $fullActionName;

    /**
     *
     * @param HelperData $helperData
     * @param RequestInterface $request
     * @param UrlInterface $url
     */
    public function __construct(
        HelperData $helperData,
        RequestInterface $request,
        UrlInterface $url,
        $fullActionName
    ) {

        $this->helperData     = $helperData;
        $this->request        = $request;
        $this->url            = $url;
        $this->fullActionName = $fullActionName;
    }

    /**
     * Retrieve robots by config settings
     *
     * @return string
     */
    public function getRobotsBySettings()
    {
        $metaRobots = '';
        //$this->modifyByProtocol($metaRobots);
        $this->modifyByPages($metaRobots, $this->helperData->getNoindexPages(), 'NOINDEX, FOLLOW');
        $this->modifyByUserPages($metaRobots, $this->helperData->getNoindexUserPages(), 'NOINDEX, FOLLOW');
        $this->modifyByUserPages($metaRobots, $this->helperData->getNoindexNofollowUserPages(), 'NOINDEX, NOFOLLOW');

        return $metaRobots;
    }

    /**
     * Retrieve robots for URL protocol
     *
     * @deprecated
     * @param string $metaRobots
     * @return string $metaRobots
     */
    protected function modifyByProtocol(&$metaRobots)
    {
        if (substr($this->url->getCurrentUrl(), 0, 8) == 'https://') {
            $metaRobots = $this->helperData->getMetaRobotsForHttps();
        }
    }

    /**
     * Retrieve robots for page settings
     *
     * @param string $metaRobots
     * @return string $metaRobots
     */
    protected function modifyByPages(&$metaRobots, $patterns, $robots)
    {
        if (empty($patterns)) {
            return;
        }
        foreach ($patterns as $pattern) {
            if (preg_match('/' . $pattern . '/', $this->request->getFullActionName())) {
                $metaRobots = $robots;
                break;
            }
        }
    }

    /**
     * Retrieve robots for user page settings
     *
     * @param string $metaRobots
     * @return string $metaRobots
     */
    protected function modifyByUserPages(&$metaRobots, $patterns, $robots)
    {
        if (empty($patterns)) {
            return;
        }
        foreach ($patterns as $pattern) {
            $pattern = str_replace(['?', '*'], ['\?', '.*?'], $pattern);
            if (preg_match('#' . $pattern . '#', $this->request->getFullActionName())
                || preg_match('#' . $pattern . '#', $this->url->getCurrentUrl())
            ) {
                $metaRobots = $robots;
                break;
            }
        }
    }
}
