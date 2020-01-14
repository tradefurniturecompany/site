<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Ui\Component\Listing\Column;

use MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;

class RequestIdentifier extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \MageWorx\SeoRedirects\Model\Redirect\Source\Category
     */
    protected $categoryOptions;

    /**
     * @var RedirectTypeEntity
     */
    protected $redirectTypeEntity;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    protected $pageCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var array|null
     */
    protected $productNames;

    /**
     * @var array|null
     */
    protected $pageTitles;

    /**
     * @var array|null
     */
    protected $landingpageHeaders;

    /**
     * @var string
     */
    protected $entityType = CustomRedirectInterface::REQUEST_ENTITY_TYPE;

    /**
     * @var string
     */
    protected $entityIdentifier = CustomRedirectInterface::REQUEST_ENTITY_IDENTIFIER;

    /** @var EventManagerInterface */
    protected $eventManager;

    /**
     * RequestIdentifier constructor.
     *
     * @param EventManagerInterface $eventManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \MageWorx\SeoRedirects\Model\Redirect\Source\Category $categoryOptions
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        EventManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \MageWorx\SeoRedirects\Model\Redirect\Source\Category $categoryOptions,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->eventManager             = $eventManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->pageCollectionFactory    = $pageCollectionFactory;
        $this->categoryOptions          = $categoryOptions;
        $this->urlBuilder               = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $productIds     = [];
            $pageIds        = [];
            $landingpageIds = [];

            foreach ($dataSource['data']['items'] as & $item) {
                switch ($item[$this->entityType]) {
                    case CustomRedirect::REDIRECT_TYPE_PRODUCT:
                        $productIds[] = $item[$this->entityIdentifier];
                        break;
                    case CustomRedirect::REDIRECT_TYPE_PAGE:
                        $pageIds[] = $item[$this->entityIdentifier];
                        break;
                    case CustomRedirect::REDIRECT_TYPE_LANDINGPAGE:
                        $landingpageIds [] = $item[$this->entityIdentifier];
                        break;
                }
            }

            foreach ($dataSource['data']['items'] as & $item) {
                if (!isset($item['redirect_id']) || !isset($item[$this->entityType])) {
                    continue;
                }

                switch ($item[$this->entityType]) {
                    case CustomRedirect::REDIRECT_TYPE_CATEGORY:
                        $categoryOptions = $this->categoryOptions->toArray();
                        if (!empty($categoryOptions[$item[$this->entityIdentifier]])) {
                            $item[$this->getData('name')] =
                                str_replace('(ID#', '<br> (ID#', $categoryOptions[$item[$this->entityIdentifier]]);
                        } else {
                            $item[$this->getData('name')] =
                                __('UNKNOWN CATEGORY') . "<br>" . ' (ID#' . $item[$this->entityIdentifier] . ')';
                        }
                        break;
                    case CustomRedirect::REDIRECT_TYPE_PRODUCT:
                        $productOptions = $this->getProductOptions($productIds);
                        if (!empty($productOptions[$item[$this->entityIdentifier]])) {
                            $item[$this->getData('name')] =
                                $productOptions[$item[$this->entityIdentifier]] . "<br>" . ' (ID#' . $item[$this->entityIdentifier] . ')';
                        } else {
                            $item[$this->getData('name')] =
                                __('UNKNOWN PRODUCT') . "<br>" . ' (ID#' . $item[$this->entityIdentifier] . ')';
                        }
                        break;
                    case CustomRedirect::REDIRECT_TYPE_PAGE:
                        $pageOptions = $this->getPageOptions($pageIds);
                        if (!empty($pageOptions[$item[$this->entityIdentifier]])) {
                            $item[$this->getData('name')] =
                                $pageOptions[$item[$this->entityIdentifier]] . "<br>" . ' (ID#' . $item[$this->entityIdentifier] . ')';
                        } else {
                            $item[$this->getData('name')] =
                                __('UNKNOWN PAGE') . "<br>" . ' (ID#' . $item[$this->entityIdentifier] . ')';
                        }
                        break;
                    case CustomRedirect::REDIRECT_TYPE_LANDINGPAGE:
                        $pageOptions = $this->getLandingPageOptions($landingpageIds);
                        if (!empty($pageOptions[$item[$this->entityIdentifier]]['header'])) {
                            $item[$this->getData('name')] =
                                $pageOptions[$item[$this->entityIdentifier]]['header'] . "<br>" . ' (ID#' . $item[$this->entityIdentifier] . ')';
                        } else {
                            $item[$this->getData('name')] =
                                __('UNKNOWN LANDING PAGE') . "<br>" . ' (ID#' . $item[$this->entityIdentifier] . ')';
                        }
                        break;
                }
            }
        }

        return $dataSource;
    }


    /**
     * @param array $productIds
     * @return array
     */
    protected function getProductOptions(array $productIds)
    {
        if ($this->productNames === null) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
            $collection = $this->productCollectionFactory->create();
            $collection->addIdFilter($productIds);
            $collection->addAttributeToSelect('name');
            $this->productNames = $collection->toOptionHash();
        }

        return $this->productNames;
    }

    /**
     * @param array $pageIds
     * @return array
     */
    protected function getPageOptions(array $pageIds)
    {
        if ($this->pageTitles === null) {
            /** @var \Magento\Cms\Model\ResourceModel\Page\Collection $collection */
            $collection = $this->pageCollectionFactory->create();

            $collection->addFieldToFilter($collection->getIdFieldName(), $pageIds);
            $collection->addFieldToSelect($collection->getIdFieldName());
            $collection->addFieldToSelect(\Magento\Cms\Api\Data\PageInterface::TITLE);

            $this->pageTitles = [];
            foreach ($collection as $item) {
                $this->pageTitles[$item->getData($collection->getIdFieldName())] =
                    $item->getData(\Magento\Cms\Api\Data\PageInterface::TITLE);
            }
        }

        return $this->pageTitles;
    }

    /**
     * @param array $landingpageIds
     * @return array
     */
    protected function getLandingPageOptions(array $landingpageIds)
    {


        if ($this->landingpageHeaders === null) {

            $data = new DataObject();
            $data->setIds($landingpageIds);
            $data->setLandingpagesData([]);
            $this->eventManager->dispatch(
                'mageworx_landingpages_get_landingpages_data',
                ['object' => $data]
            );

            $this->landingpageHeaders = $data->getLandingpagesData();
        }

        return $this->landingpageHeaders;
    }
}
