<?php
/**
 * Copyright ©  MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Info\Block\Adminhtml;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\View\Element\Template;

class Extensions extends Template
{
    /**
     * @var \MageWorx\Info\Helper\Data
     */
    protected $helper;

    /**
     * @var \MageWorx\Info\Model\MetaPackageList
     */
    protected $metaPackageList;

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var string
     */
    protected $_template = 'MageWorx_Info::extensions.phtml';

    /**
     * Extensions constructor.
     *
     * @param \MageWorx\Info\Model\MetaPackageList $metaPackageList
     * @param \Magento\Framework\Component\ComponentRegistrarInterface $componentRegistrar
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param \MageWorx\Info\Helper\Data $helper
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \MageWorx\Info\Model\MetaPackageList $metaPackageList,
        \MageWorx\Info\Helper\Data $helper,
        DataObjectFactory $dataObjectFactory,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->metaPackageList   = $metaPackageList;
        $this->helper            = $helper;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @return array
     */
    public function getRecommendedExtensionsData()
    {
        $data = $this->helper->getRecommendedExtensionsData();

        $result = [];

        if (is_array($data)) {
            foreach ($data as $id => $extData) {
                $result[$id] = $this->dataObjectFactory->create()->setData($extData);
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getInstalledExtensionsData()
    {
        $data   = $this->helper->getInstalledExtensionsData();
        $result = [];

        if (is_array($data)) {
            foreach ($data as $id => $extData) {
                $result[$id] = $this->dataObjectFactory->create()->setData($extData);
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getReviewUrl()
    {
        return $this->helper->getReviewUrl();
    }

    /**
     * @param array $installedExts
     * @return array
     */
    public function prepareMarketplaceReviewUrls($installedExts)
    {
        $result = [];
        foreach ($installedExts as $code => $extension) {
            $result[$code] = $this->escapeUrl($extension->getMarketplaceLink());
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getStoreUrl()
    {
        return $this->helper->getStoreUrl();
    }

    /**
     * @param string $name
     * @return string
     */
    public function getExtensionVersion($name)
    {
        return $this->metaPackageList->getInstalledVersion($name);
    }
}