<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Plugin\NextPrev;

use MageWorx\SeoBase\Helper\Data as HelperData;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Layout;
use MageWorx\SeoBase\Model\NextPrevFactory;

class ResponseHttpBefore
{

    /**
     * @var  \MageWorx\SeoBase\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

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

    public function __construct(
        HelperData $helperData,
        Registry $registry,
        RequestInterface $request,
        UrlInterface $url,
        Layout $layout,
        NextPrevFactory $nextPrevFactory
    ) {
        $this->helperData = $helperData;
        $this->registry = $registry;
        $this->request = $request;
        $this->url = $url;
        $this->layout = $layout;
        $this->nextPrevFactory = $nextPrevFactory;
    }

    /**
     * Add next/prev link relations
     *
     * @param \Magento\Framework\App\Response\Http $subject
     * @param string $value
     * @return array
     */
    public function beforeAppendBody($subject, $value)
    {
        if (!$this->helperData->useNextPrev()) {
            return [$value];
        }
        if (is_callable([$subject, 'isAjax']) && $subject->isAjax()) {
            return [$value];
        }
        $fullActionName = $this->request->getFullActionName();
        /** @var \MageWorx\SeoBase\Model\NextPrev */
        $nextPrevInstance = $this->nextPrevFactory->create($fullActionName, $arguments = []);
        if ($nextPrevInstance) {
            $nextUrl = $nextPrevInstance->getNextUrl();
            $prevUrl = $nextPrevInstance->getPrevUrl();

            if (!empty($prevUrl)) {
                $prevStr = '<link rel="prev" href="' . $prevUrl . '" />';
                $value = str_ireplace('</head>', "\n" . $prevStr . '</head>', $value);
            }

            if (!empty($nextUrl)) {
                $nextStr = '<link rel="next" href="' . $nextUrl . '" /> ';
                $value = str_ireplace('</head>', "\n" . $nextStr . '</head>', $value);
            }
        }
        return [$value];
    }
}
