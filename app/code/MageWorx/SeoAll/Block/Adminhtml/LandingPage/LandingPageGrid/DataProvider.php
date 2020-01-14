<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Block\Adminhtml\LandingPage\LandingPageGrid;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;

/**
 * Class
 *
 */
class DataProvider
{
    /**
     * @var AttributeCollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @var array
     */
    protected $optionData = [];

    /**
     * @var array
     */
    protected $attributeData = [];

    /**
     * AbstractConverter constructor.
     *
     * @param AttributeCollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        AttributeCollectionFactory $attributeCollectionFactory
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * @param int $attributeId
     * @param int $optionId
     * @return mixed
     */
    public function getOptionLabel($attributeId, $optionId)
    {
        if (empty($this->attributeData[$attributeId][$optionId]['label'])) {
            return '';
        }
        return $this->optionData[$attributeId][$optionId]['label'];
    }

    /**
     * @param int $attributeId
     * @return string
     */
    public function getAttributeLabel($attributeId)
    {
        if (empty($this->attributeData[$attributeId]['label'])) {
            return '';
        }
        return $this->attributeData[$attributeId]['label'];
    }
    /**
     * @param array|int $attributeIds
     * @return $this
     */
    public function prepareAttributes($attributeIds)
    {
        $attributeData = [];
        $optionData = [];
        $collection = $this->attributeCollectionFactory->create();
        $collection->addFieldToFilter('attribute_id', $attributeIds);
        $collection->addFieldToSelect('*');

        foreach ($collection as $item) {
            $attributeData[$item->getId()]['attribute_code'] = $item->getData('attribute_code');
            $attributeData[$item->getId()]['label'] = $item->getData('frontend_label');
            $options = $item->getSource()->getAllOptions();
            foreach ($options as $option) {
                $optionId = $option['value'];
                $optionData[$item->getId()][$optionId]['label'] = $option['label'];
            }
        }

        $this->attributeData = $attributeData;
        $this->optionData = $optionData;

        return $this;
    }
}