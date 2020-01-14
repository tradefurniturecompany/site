<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Controller\Adminhtml\Categoryfilter;

use MageWorx\SeoExtended\Controller\Adminhtml\Categoryfilter as CategoryfilterController;
use MageWorx\SeoExtended\Model\CategoryFilter;

class InlineEdit extends CategoryfilterController
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \MageWorx\SeoExtended\Model\CategoryFilterFactory
     */
    protected $categoryFilterFactory;

    /**
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Framework\Registry $registry
     * @param \MageWorx\SeoExtended\Model\CategoryFilterFactory $categoryFilterFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\Registry $registry,
        \MageWorx\SeoExtended\Model\CategoryFilterFactory $categoryFilterFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->categoryFilterFactory = $categoryFilterFactory;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($registry, $context);
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
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $categoryFilterId) {
            /** @var \MageWorx\SeoExtended\Model\CategoryFilter $categoryFilter */
            $categoryFilter = $this->categoryFilterFactory->create()->load($categoryFilterId);
            try {
                $categoryFilterData = $this->prepareData($postItems[$categoryFilterId]);
                $categoryFilter->addData($categoryFilterData);
                $categoryFilter->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getError($categoryFilter, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getError($categoryFilter, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getError(
                    $categoryFilter,
                    __('Something went wrong while saving the page.')
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
     * Add category filter id to error message
     *
     * @param CategoryFilter $categoryFilter
     * @param $errorText
     * @return string
     */
    protected function getError(CategoryFilter $categoryFilter, $errorText)
    {
        return '[Category Filter ID: ' . $categoryFilter->getId() . '] ' . $errorText;
    }
}
