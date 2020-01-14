<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect;

use MageWorx\SeoRedirects\Api\Data;
use MageWorx\SeoRedirects\Api\CustomRedirectRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect as ResourceCustomRedirect;
use MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\CollectionFactory as CustomRedirectCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * CustomRedirectRepository
 */
class CustomRedirectRepository implements CustomRedirectRepositoryInterface
{
    /**
     * @var ResourceCustomRedirect
     */
    protected $resource;

    /**
     * @var CustomRedirectFactory
     */
    protected $customRedirectFactory;

    /**
     * @var CustomRedirectCollectionFactory
     */
    protected $customRedirectCollectionFactory;

    /**
     * @var Data\CustomRedirectSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \MageWorx\SeoRedirects\Api\Data\CustomRedirectInterfaceFactory
     */
    protected $dataCustomRedirectFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceCustomRedirect $resource
     * @param CustomRedirectFactory $customRedirectFactory
     * @param Data\CustomRedirectInterfaceFactory $dataCustomRedirectFactory
     * @param CustomRedirectCollectionFactory $customRedirectCollectionFactory
     * @param Data\CustomRedirectSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceCustomRedirect $resource,
        CustomRedirectFactory $customRedirectFactory,
        Data\CustomRedirectInterfaceFactory $dataCustomRedirectFactory,
        CustomRedirectCollectionFactory $customRedirectCollectionFactory,
        Data\CustomRedirectSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource                        = $resource;
        $this->customRedirectFactory           = $customRedirectFactory;
        $this->customRedirectCollectionFactory = $customRedirectCollectionFactory;
        $this->searchResultsFactory            = $searchResultsFactory;
        $this->dataObjectHelper                = $dataObjectHelper;
        $this->dataCustomRedirectFactory       = $dataCustomRedirectFactory;
        $this->dataObjectProcessor             = $dataObjectProcessor;
        $this->storeManager                    = $storeManager;
    }

    /**
     * Save CustomRedirect data
     *
     * @param \MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface $customRedirect
     * @return CustomRedirect
     * @throws CouldNotSaveException
     */
    public function save(\MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface $customRedirect)
    {
        if (empty($customRedirect->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $customRedirect->setStoreId($storeId);
        }
        try {
            $this->resource->save($customRedirect);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the redirect: %1',
                    $exception->getMessage()
                )
            );
        }

        return $customRedirect;
    }

    /**
     * Load CustomRedirect data by given CustomRedirect Identity
     *
     * @param string $customRedirectId
     * @return CustomRedirect
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($customRedirectId)
    {
        $customRedirect = $this->customRedirectFactory->create();
        $customRedirect->load($customRedirectId);
        if (!$customRedirect->getId()) {
            throw new NoSuchEntityException(__('Custom Redirect with id "%1" does not exist.', $customRedirectId));
        }

        return $customRedirect;
    }

    /**
     * Load CustomRedirect data collection by given search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\Collection
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->customRedirectCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurCustomRedirect($criteria->getCurrentCustomRedirect());
        $collection->setCustomRedirectSize($criteria->getCustomRedirectSize());
        $customRedirects = [];
        /** @var CustomRedirect $customRedirectModel */
        foreach ($collection as $customRedirectModel) {
            $customRedirectData = $this->dataCustomRedirectFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $customRedirectData,
                $customRedirectModel->getData(),
                'MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface'
            );
            $customRedirects[] = $this->dataObjectProcessor->buildOutputDataArray(
                $customRedirectData,
                'MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface'
            );
        }
        $searchResults->setItems($customRedirects);

        return $searchResults;
    }

    /**
     * Delete CustomRedirect
     *
     * @param \MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface $customRedirect
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface $customRedirect)
    {
        try {
            $this->resource->delete($customRedirect);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the redirect: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    /**
     * Delete CustomRedirect by given CustomRedirect Identity
     *
     * @param string $customRedirectId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($customRedirectId)
    {
        return $this->delete($this->getById($customRedirectId));
    }
}
