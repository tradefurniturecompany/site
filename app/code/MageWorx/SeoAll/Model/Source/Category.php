<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Model\Source;

use MageWorx\SeoAll\Model\Source;

class Category extends Source
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Tree
     */
    protected $categoryTreeResource;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    protected $options = null;

    /**
     * Category constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTreeResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTreeResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryTreeResource = $categoryTreeResource;
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @param array $specialCategories
     * @param string $action disable or mark
     * @param string $markLabel
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray($specialCategories = [], $action = 'disable', $markLabel = '')
    {

        if ($this->options === null) {
            $this->options[] = ['value' => '', 'label' => ''];

            foreach ($this->getCategoryTree() as $category) {
                $params['value'] = $category['value'];
                $params['label'] = $category['label'];

                if (in_array($category['value'], $specialCategories)) {

                    if ($action == 'disable') {
                        $params['disabled'] = 1;
                    } elseif ($action == 'mark') {
                        $params['label'] =  $params['label'] . $markLabel;
                        unset($params['disabled']);
                    }
                } else {
                    unset($params['disabled']);
                }

                $this->options[] = $params;
            }
        }

        return $this->options;
    }

    /**
     *
     * @param Varien_Data_Tree_Node $node
     * @param array $values
     * @param int $level
     * @return array
     */
    protected function createCategoryTree($node, $values, $level = 0)
    {
        $level++;

        /* for case if category doesn't have parent category */
        if ($node == null) {
            return [];
        }

        $values[$node->getId()]['value'] =  $node->getId();
        $values[$node->getId()]['label'] = str_repeat("--/", $level-1) . $node->getName() . ' (ID#' . $node->getId() . ')';
        $values[$node->getId()]['disabled'] = true;

        foreach ($node->getChildren() as $child) {
            $values = $this->createCategoryTree($child, $values, $level);
        }

        return $values;
    }

    /**
     *
     * @return array
     */
    protected function getCategoryTree()
    {
        $parentId = $this->storeManager->getStore()->getRootCategoryId();

        $tree = $this->categoryTreeResource->load();
        $root = $tree->getNodeById($parentId);

        if ($root && $root->getId() == 1) {
            $root->setName(__('Root'));
        }

        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection
            ->setStoreId(0)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active');


        $tree->addCollectionData($categoryCollection, true);

        return $this->createCategoryTree($root, []);
    }
}
