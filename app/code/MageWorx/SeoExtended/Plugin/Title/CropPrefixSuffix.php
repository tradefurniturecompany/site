<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Plugin\Title;

class CropPrefixSuffix
{
    /**
     *
     * @var \MageWorx\SeoExtended\Helper\Data
     */
    protected $helperData;

    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     *
     * @param \MageWorx\SeoExtended\Helper\Data $helperData
     */
    public function __construct(
        \MageWorx\SeoExtended\Helper\Data $helperData,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->helperData = $helperData;
        $this->request    = $request;
    }

    /**
     * Crop suffix and prefix by action
     *
     * @param \Magento\Framework\View\Page\Title $subject
     * @param string $result
     * @return string
     */
    public function afterGet($subject, $result)
    {
        if (!($subject instanceof \Magento\Framework\View\Page\Title)) {
            return $result;
        }

        if ($this->helperData->isCutMagentoPrefixSuffixByPage($this->request->getFullActionName())) {
            return $subject->getShortHeading();
        }

        return $result;
    }
}
