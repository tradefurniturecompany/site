<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Controller\Adminhtml\Categoryfilter;

use MageWorx\SeoExtended\Model\CategoryFilter;

class MassDelete extends MassAction
{
    /**
     * @var \MageWorx\SeoExtended\Api\CategoryFilterRepositoryInterface
     */
    protected $categoryFilterRepository;

    /**
     * MassDelete constructor.
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \MageWorx\SeoExtended\Model\CategoryFilterFactory $categoryFilterFactory
     * @param \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\CollectionFactory $categoryFilterCollectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \MageWorx\SeoExtended\Api\CategoryFilterRepositoryInterface $categoryFilterRepository,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \MageWorx\SeoExtended\Model\CategoryFilterFactory $categoryFilterFactory,
        \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\CollectionFactory $categoryFilterCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->categoryFilterRepository = $categoryFilterRepository;
        parent::__construct($filter, $categoryFilterFactory, $categoryFilterCollectionFactory, $registry, $context);
    }

    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 record(s) have been deleted';
    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while deleting record(s).';

    /**
     * @param CategoryFilter $categoryFilter
     * @return $this
     */
    protected function executeAction(CategoryFilter $categoryFilter)
    {
        $this->categoryFilterRepository->delete($categoryFilter);
        return $this;
    }
}
