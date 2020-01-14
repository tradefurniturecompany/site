<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoBreadcrumbs\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\SeoBreadcrumbs\Controller\Adminhtml\Category as BreadcrumbsCategoryController;
use Magento\Framework\Registry;

class InlineEdit extends BreadcrumbsCategoryController
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * InlineEdit constructor.
     * @param JsonFactory $jsonFactory
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        Registry $registry,
        CategoryFactory $categoryFactory,
        Context $context
    ) {
        $this->jsonFactory = $jsonFactory;
        parent::__construct($registry, $categoryFactory, $context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the sent data.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $categoryId) {
            /** @var \Magento\Catalog\Model\Category $category */
            $category = $this->categoryFactory->create()->load($categoryId);
            try {
                $categoryData = $this->filterData($postItems[$categoryId]);
                $category->addData($categoryData);
                $category->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithCategoryId($category, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithCategoryId($category, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithCategoryId(
                    $category,
                    __('Something went wrong while saving the category.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add category id to error message
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithCategoryId(Category $category, $errorText)
    {
        return '[Category ID: ' . $category->getId() . '] ' . $errorText;
    }
}
