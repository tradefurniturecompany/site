<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCategoryGrid\Ui\DataProvider;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;
use Magento\Framework\Api\Filter;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

class CategoryGridDataProvider extends AbstractDataProvider
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    protected $collection;

    /**
     * @var AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * CategoryGridDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->addFilterStrategies = $addFilterStrategies;
        $this->storeManager        = $storeManager;
        $this->collection          = $collectionFactory->create();

        $this->prepareCollection();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items'        => array_values($items),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(Filter $filter)
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }

    /**
     * Basic collection preparation
     */
    protected function prepareCollection()
    {
        $this->collection
            ->setStore($this->storeManager->getStore(Store::DEFAULT_STORE_ID))
            ->addFieldToFilter('level', ['nin' => [0, 1]]);
    }
}
