<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\SeoExtended\Api\CategoryFilterRepositoryInterface;
use MageWorx\SeoExtended\Api\Data\CategoryFilterInterface;
use MageWorx\SeoExtended\Api\Data\CategoryFilterInterfaceFactory;
use MageWorx\SeoExtended\Api\Data\CategoryFilterSearchResultsInterfaceFactory;
use MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter as ResourceCategoryFilter;
use MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\Collection;
use MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\CollectionFactory as CategoryFilterCollectionFactory;

class CategoryFilterRepository implements CategoryFilterRepositoryInterface
{
    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var CategoryFilter
     */
    protected $resource;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryFilterCollectionFactory
     */
    protected $categoryFilterCollectionFactory;

    /**
     * @var CategoryFilterSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var CategoryFilterInterfaceFactory
     */
    protected $categoryFilterInterfaceFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    public function __construct(
        ResourceCategoryFilter $resource,
        StoreManagerInterface $storeManager,
        CategoryFilterCollectionFactory $categoryFilterCollectionFactory,
        CategoryFilterSearchResultsInterfaceFactory $categoryFilterSearchResultsInterfaceFactory,
        CategoryFilterInterfaceFactory $categoryFilterInterfaceFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource                         = $resource;
        $this->storeManager                     = $storeManager;
        $this->categoryFilterCollectionFactory  = $categoryFilterCollectionFactory;
        $this->searchResultsFactory             = $categoryFilterSearchResultsInterfaceFactory;
        $this->categoryFilterInterfaceFactory   = $categoryFilterInterfaceFactory;
        $this->dataObjectHelper                 = $dataObjectHelper;
    }
    /**
     * Save Category Filter
     *
     * @param \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface $categoryFilter
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CategoryFilterInterface $categoryFilter)
    {
        /** @var CategoryFilterInterface|\Magento\Framework\Model\AbstractModel $categoryFilter */
        try {
            $this->resource->save($categoryFilter);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the category filter: %1',
                $exception->getMessage()
            ));
        }
        return $categoryFilter;
    }

    /**
     * Retrieve Category Filter
     *
     * @param int $categoryFilterId
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($categoryFilterId)
    {
        if (!isset($this->instances[$categoryFilterId])) {
            /** @var \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface $categoryFilter */
            $categoryFilter = $this->categoryFilterInterfaceFactory->create();
            //$this->resource->load($categoryFilter, $categoryFilterId);
            $categoryFilter->load($categoryFilterId);
            if (!$categoryFilter->getId()) {
                throw new NoSuchEntityException(__('Requested category filter entity doesn\'t exist'));
            }
            $this->instances[$categoryFilterId] = $categoryFilter;
        }
        return $this->instances[$categoryFilterId];
    }

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \MageWorx\SeoExtended\Api\Data\CategoryFilterSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\Collection $collection */
        $collection = $this->categoryFilterCollectionFactory->create();

        //Add filters from root filter group to the collection
        /** @var FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        /** @var SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        } else {
            // set a default sorting order since this method is used constantly in many
            // different blocks
            $field = 'id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface[] $categoryFilters */
        $categoryFilters = [];
        /** @var \MageWorx\SeoExtended\Model\CategoryFilter $categoryFilter */
        foreach ($collection as $categoryFilter) {
            /** @var \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface $categoryFilterDataObject */
            $categoryFilterDataObject = $this->categoryFilterInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray($categoryFilterDataObject, $categoryFilter->getData(), CategoryFilterInterface::class);
            $categoryFilters[] = $categoryFilterDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($categoryFilters);
    }

    /**
     * Delete category filter
     *
     * @param \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface $categoryFilter
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CategoryFilterInterface $categoryFilter)
    {
        /** @var \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface|\Magento\Framework\Model\AbstractModel $categoryFilter */
        $id = $categoryFilter->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($categoryFilter);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove category filter entity %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete category filter by ID
     *
     * @param int $categoryFilterId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($categoryFilterId)
    {
        $categoryFilter = $this->getById($categoryFilterId);
        return $this->delete($categoryFilter);
    }

    /**
     * Helper function that adds a FilterGroup to the collection
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
        return $this;
    }
}
