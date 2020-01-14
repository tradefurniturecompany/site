<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoBreadcrumbs\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Registry;

abstract class Category extends Action
{
    /**
     * Category factory
     *
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        CategoryFactory $categoryFactory,
        Context $context
    ) {
        $this->coreRegistry = $registry;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Catalog\Model\Category
     */
    protected function initCategory()
    {
        $categoryId = $this->getRequest()->getParam('category_id');

        $category = $this->categoryFactory->create();
        if ($category) {
            $category->load($categoryId);
        }
        $this->coreRegistry->register('mageworx_seobreadcrumbs_category', $category);
        return $category;
    }

    /**
     * filter dates
     *
     * @param array $data
     * @return array
     */
    public function filterData($data)
    {
        $breadcrumbsPriority = \MageWorx\SeoBreadcrumbs\Helper\Data::BREADCRUMBS_PRIORITY_CODE;

        $data[$breadcrumbsPriority] = (int)$data[$breadcrumbsPriority];
        return $data;
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_SeoBreadcrumbs::category');
    }
}
