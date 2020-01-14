<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Model\DbWriter;

use Magento\Framework\App\ResourceConnection;
use MageWorx\SeoXTemplates\Model\DataProviderLandingPageFactory;
use MageWorx\SeoAll\Helper\LinkFieldResolver;

class LandingPage extends \MageWorx\SeoXTemplates\Model\DbWriter
{
    /**
     * @var DataProviderLandingPageFactory
     */
    protected $dataProviderLandingPageFactory;

    /**
     * @var \MageWorx\SeoAll\Helper\LinkFieldResolver
     */
    protected $linkFieldResolver;

    /**
     * LandingPage constructor.
     *
     * @param ResourceConnection $resource
     * @param DataProviderLandingPageFactory $dataProviderLandingPageFactory
     * @param LinkFieldResolver $linkFieldResolver
     */
    public function __construct(
        ResourceConnection $resource,
        DataProviderLandingPageFactory $dataProviderLandingPageFactory,
        LinkFieldResolver $linkFieldResolver
    ) {
        parent::__construct($resource);
        $this->dataProviderLandingPageFactory = $dataProviderLandingPageFactory;
        $this->linkFieldResolver = $linkFieldResolver;
    }

    /**
     * @param \Magento\Framework\Data\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param null $customStoreId
     * @return array|bool
     * @throws \Exception
     */
    public function write($collection, $template, $customStoreId = null)
    {
        if (!$collection) {
            return false;
        }

        $dataProvider = $this->dataProviderLandingPageFactory->create($template->getTypeId());
        $data         = $dataProvider->getData($collection, $template, $customStoreId);
        foreach ($collection as $landingPage) {
            if (empty($data[$landingPage->getId()])) {
                continue;
            }

            $filterData = $data[$landingPage->getId()];

            if (!$filterData['value']) {
                continue;
            }
            $landingPage->setStoreValue($filterData['target_property'], $filterData['value'], $filterData['store_id']);
            $landingPage->save();
        }

        return true;
    }
}
