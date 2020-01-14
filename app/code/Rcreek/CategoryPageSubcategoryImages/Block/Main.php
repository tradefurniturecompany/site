<?php

namespace Rcreek\CategoryPageSubcategoryImages\Block;

use Magento\Framework\Registry;

class Main extends \Magento\Framework\View\Element\Template
{
    private $registry;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        Registry $registry
    )
    {
        parent::__construct($context);
        $this->registry = $registry;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function getSubcategories()
    {
        $category = $this->registry->registry('current_category');

        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addIsActiveFilter();
        $collection->addLevelFilter(3);
        $collection->addPathsFilter($category->getPath().'/');

        if($category->getName() === 'Collections') {
            $collection->addAttributeToFilter('include_in_menu', ['eq' => 1]);
        } else {
            $collection->addAttributeToFilter('include_in_menu', ['eq' => 0]);
        }
        $collection->addOrderField('position');

        return $collection;
    }
}
