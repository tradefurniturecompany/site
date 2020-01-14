<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Observer;


class ClearByDeletedAttributeValueObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * ClearByDeletedAttributeValueObserver constructor.
     *
     * @param \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\CollectionFactory $collectionFactory
     */
    public function __construct(
        \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $attribute  = $observer->getAttribute();
        $deletedIds = $this->getDeletedAttributeOptionIds($attribute);

        if (!empty($deletedIds)) {

            /** @var \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('attribute_option_id', ['in' => $deletedIds]);
            $collection->walk('delete');
        }

        return $this;
    }

    /**
     * @param $attribute
     * @return array
     */
    protected function getDeletedAttributeOptionIds($attribute)
    {
        $deletedIds = [];

        $data = $attribute->getData();

        if (!empty($data['option']['delete'])) {

            $deletedData = array_filter(
                $data['option']['delete'],
                function ($element) {
                    return $element == 1;
                }
            );

            $deletedIds = array_filter(array_keys($deletedData), 'is_numeric');
        }

        return $deletedIds;
    }
}