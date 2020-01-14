<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Controller\Adminhtml;

use MageWorx\SeoExtended\Api\CategoryFilterRepositoryInterface;
use MageWorx\SeoExtended\Model\CategoryFilterFactory;
use MageWorx\SeoExtended\Controller\RegistryConstants;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;

class CategoryFilterHelper
{
    /**
     * @var \MageWorx\SeoExtended\Api\CategoryFilterRepositoryInterface
     */
    protected $categoryFilterRepository;

    /**
     * @var CategoryFilterFactory
     */
    protected $categoryFilterFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * CategoryFilterHelper constructor
     * @param CategoryFilterRepositoryInterface $categoryFilterRepository
     * @param CategoryFilterFactory $categoryFilterFactory
     * @param RequestInterface $request
     * @param Registry $coreRegistry
     */
    public function __construct(
        CategoryFilterRepositoryInterface $categoryFilterRepository,
        CategoryFilterFactory $categoryFilterFactory,
        RequestInterface $request,
        Registry $coreRegistry
    ) {
        $this->categoryFilterRepository = $categoryFilterRepository;
        $this->categoryFilterFactory = $categoryFilterFactory;
        $this->request = $request;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function initCategoryFilter()
    {
        $categoryFilterId = $this->request->getParam('id');

        if ($categoryFilterId) {
            $categoryFilter = $this->categoryFilterRepository->getById($categoryFilterId);
        } else {
            $categoryFilter = $this->categoryFilterFactory->create();
        }

        $this->coreRegistry->register(RegistryConstants::CURRENT_CATEGORY_FILTER_CONSTANT, $categoryFilter);
        return $categoryFilter;
    }
}
