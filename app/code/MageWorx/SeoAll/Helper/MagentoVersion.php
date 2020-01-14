<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Helper;

/**
 * Class MagentoVersion
 */
class MagentoVersion extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    protected $productMetadata;

    /**
     * MagentoVersion constructor.
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\App\Helper\Context $context
    ) {
    
        $this->productMetadata = $productMetadata;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->productMetadata->getVersion();
    }
}
