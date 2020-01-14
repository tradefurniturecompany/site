<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model\LengthDataProvider;

class MetaDescription implements \MageWorx\SeoReports\Model\LengthDataProviderInterface
{
    /**
     * @var \MageWorx\SeoReports\Helper\Data
     */
    protected $helperData;

    /**
     * MetaTitle constructor.
     *
     * @param \MageWorx\SeoReports\Helper\Data $helperData
     */
    public function __construct(\MageWorx\SeoReports\Helper\Data $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxLength()
    {
        return $this->helperData->getMaxLengthMetaDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function getMinLength()
    {
        return null;
    }
}