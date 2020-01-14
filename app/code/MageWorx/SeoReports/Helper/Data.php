<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var
     */
    protected $helperLength;

    /**
     * Data constructor.
     *
     * @param \MageWorx\SeoAll\Helper\Length $helperLength
     * @param Context $context
     */
    public function __construct(
        \MageWorx\SeoAll\Helper\Length $helperLength,
        Context $context
    ) {
        parent::__construct($context);
        $this->helperLength = $helperLength;
    }

    /**
     * @return int
     */
    public function getMaxLengthMetaTitle()
    {
        return $this->helperLength->getMetaTitleMaxLength();
    }

    /**
     * @return int
     */
    public function getMaxLengthMetaDescription()
    {
        return $this->helperLength->getMetaDescriptionMaxLength();
    }

    /**
     * @return int
     */
    public function getMaxLengthUrlKey()
    {
        return $this->helperLength->getUrlMaxLength();
    }

    /**
     * @return int
     */
    public function getMaxLengthH1Content()
    {
        return $this->helperLength->getH1MaxLength();
    }
}
