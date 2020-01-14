<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Categories tree block
 */
namespace MageWorx\SeoXTemplates\Block\Adminhtml\Template\Category;

use Magento\Catalog\Model\ResourceModel\Category\Collection;

class Tree extends \Magento\Catalog\Block\Adminhtml\Category\Tree
{
    /**
     * Retrieve list of categories with name containing $namePart and their parents
     *
     * @param string $namePart
     * @return string
     */
    public function getSuggestedCategoriesJson($namePart)
    {
        /* @var $collection Collection */
        $collection = $this->_categoryFactory->create()->getCollection();

        $matchingNamesCollection = $this->getMatchingNamesCollection($collection, $namePart);

        $shownCategoriesIds = [];
        foreach ($matchingNamesCollection as $category) {
            $parentIds = explode('/', $category->getPath());
            foreach ($parentIds as $parentId) {
                $shownCategoriesIds[] = $parentId;
            }
        }

        $collection->addAttributeToFilter(
            'entity_id',
            ['in' => array_unique($shownCategoriesIds)]
        )->addAttributeToSelect(
            ['name', 'parent_id', 'is_active']
        )->setStoreId(
            $this->getStoreId()
        );

        $categoryById = [
            \Magento\Catalog\Model\Category::TREE_ROOT_ID => [
                'id' => \Magento\Catalog\Model\Category::TREE_ROOT_ID,
                'children' => [],
            ],
        ];

        $template   = $this->_coreRegistry->registry('mageworx_seoxtemplates_template');
        $excludeIds = $template->getCategoryIdsAssignedForAnalogTemplate();

        foreach ($collection as $category) {

            if ($namePart) {
                if (in_array($category->getId(), $excludeIds)) {
                    continue;
                }
                if (!$category->getIsActive()) {
                    continue;
                }
            }

            foreach ([$category->getId(), $category->getParentId()] as $categoryId) {
                if (!isset($categoryById[$categoryId])) {
                    $categoryById[$categoryId] = [
                        'id' => $categoryId,
                        'children' => []
                    ];
                }
            }
            $categoryById[$category->getId()]['is_active'] = $category->getIsActive();

            if (!$namePart && in_array($category->getId(), $excludeIds)) {
                $categoryById[$category->getId()]['is_active'] = 0;
            }

            $categoryById[$category->getId()]['label'] = $category->getName();
            $categoryById[$category->getParentId()]['children'][] = & $categoryById[$category->getId()];
        }

        $result = $categoryById[\Magento\Catalog\Model\Category::TREE_ROOT_ID]['children'];
        return $this->_jsonEncoder->encode($result);
    }

    /**
     * @param Collection $collection
     * @param string $namePart
     * @return Collection
     */
    protected function getMatchingNamesCollection($collection, $namePart)
    {
        $matchingNamesCollection = clone $collection;
        $escapedNamePart = $this->_resourceHelper->addLikeEscape($namePart, ['position' => 'any']);

        $matchingNamesCollection->addAttributeToSelect(
            'path'
        )->addAttributeToFilter(
            'entity_id',
            ['neq' => \Magento\Catalog\Model\Category::TREE_ROOT_ID]
        )->addAttributeToFilter(
            'name',
            ['like' => $escapedNamePart]
        )->setStoreId($this->getStoreId());

        return $matchingNamesCollection;
    }

    /**
     * @return int
     */
    protected function getStoreId()
    {
        return $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
    }
}
