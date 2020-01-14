<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Helper;

/**
 * SEO LinkFieldResolver helper
 */
class LinkFieldResolver extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \MageWorx\SeoAll\Helper\MagentoVersion
     */
    protected $helperMagentoVersion;

    /**
     * LinkFieldResolver constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \MageWorx\SeoAll\Helper\MagentoVersion $magentoVersion,
        \Magento\Framework\App\Helper\Context $context
    ) {
    
        $this->objectManager = $objectManager;
        $this->helperMagentoVersion = $magentoVersion;
        parent::__construct($context);
    }

    /**
     * @param string $class
     * @return string
     */
    public function getLinkField($class, $field)
    {
        if (version_compare($this->helperMagentoVersion->getVersion(), '2.1.0', '>=')) {
            $this->metadataPool = $this->objectManager->get('\Magento\Framework\EntityManager\MetadataPool');
            return $this->metadataPool->getMetadata($class)->getLinkField();
        }

        return $field;
    }
}
