<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\DataProvider;

use Magento\Framework\App\ResourceConnection;
use MageWorx\SeoXTemplates\Model\ConverterLandingPageFactory;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Store\Model\StoreManagerInterface;

class LandingPage extends \MageWorx\SeoXTemplates\Model\DataProvider
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     *
     * @var int
     */
    protected $_defaultStore;

    /**
     * Store ID for obtaining and preparing data
     *
     * @var int
     */
    protected $_storeId;

    /**
     * @var HelperStore
     */
    protected $helperStore;

    /**
     *
     * @var array
     */
    protected $_attributeCodes = [];

    /**
     *
     * @var \Magento\Framework\Data\Collection
     */
    protected $_collection;

    /**
     * @var ConverterLandingPageFactory
     */
    protected $converterLandingPageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * LandingPage constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resource
     * @param ConverterLandingPageFactory $converterLandingPageFactory
     * @param HelperStore $helperStore
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ResourceConnection $resource,
        ConverterLandingPageFactory $converterLandingPageFactory,
        HelperStore $helperStore
    ) {
        parent::__construct($resource);
        $this->converterLandingPageFactory = $converterLandingPageFactory;
        $this->helperStore                 = $helperStore;
        $this->storeManager                = $storeManager;
    }

    /**
     * @param \Magento\Framework\Data\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param null $customStoreId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData($collection, $template, $customStoreId = null)
    {
        $data = [];

        $targetPropertyList = $template->getAttributeCodesByType();
        $targetProperty     = $targetPropertyList[0];
        $storeId    = $this->getStoreId($template, $customStoreId);
        /** @var \MageWorx\SeoXTemplates\Model\Template\LandingPage $landingPage */
        foreach ($collection as $landingPage) {
            $converter      = $this->converterLandingPageFactory->create($targetProperty);
            $landingPage->setStoreId($storeId);
            $attributeValue = $converter->convert($landingPage, $template->getCode());
            $data[$landingPage->getId()] = [
                'landingpage_id'  => $landingPage->getId(),
                'title'           => $landingPage->getTitle(),
                'store_id'        => $storeId,
                'store_name'      => $this->storeManager->getStore($storeId)->getName(),
                'target_property' => $targetProperty,
                'old_value'       => $landingPage->getStoreValue($targetProperty, $storeId, true),
                'value'           => $attributeValue
            ];
        }

        return $data;
    }

    /**
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param int|null $customStoreId
     * @return int|null
     */
    protected function getStoreId($template, $customStoreId = null)
    {
        if ($template->getUseForDefaultValue()) {
            return $template->getStoreId();
        }

        if ($customStoreId) {
            return $customStoreId;
        }

        if ($template->getIsSingleStoreMode()) {
            return $this->helperStore->getCurrentStoreId();
        }

        return $template->getStoreId();
    }
}
