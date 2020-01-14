<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Controller\Adminhtml\Categoryfilter;

use MageWorx\SeoExtended\Controller\Adminhtml\Categoryfilter as  CategoryfilterController;

use Magento\Framework\Exception\LocalizedException;

abstract class MassAction extends CategoryfilterController
{
    /**
     * @var \MageWorx\SeoExtended\Model\CategoryFilterFactory
     */
    protected $categoryFilterFactory;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\CollectionFactory
     */
    protected $categoryFilterCollectionFactory;

    /**
     * @var string
     */
    protected $successMessage = 'Mass Action successful on %1 records';

    /**
     * @var string
     */
    protected $errorMessage = 'Mass Action failed';


    public function __construct(
        \Magento\Ui\Component\MassAction\Filter $filter,
        \MageWorx\SeoExtended\Model\CategoryFilterFactory $categoryFilterFactory,
        \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\CollectionFactory $categoryFilterCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->filter = $filter;
        $this->categoryFilterFactory = $categoryFilterFactory;
        $this->categoryFilterCollectionFactory = $categoryFilterCollectionFactory;
        parent::__construct($registry, $context);
    }

    /**
     * @param \MageWorx\SeoExtended\Model\CategoryFilter $categoryFilter
     * @return mixed
     */
    abstract protected function executeAction(\MageWorx\SeoExtended\Model\CategoryFilter $categoryFilter);

    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->categoryFilterCollectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $categoryFilter) {
                $this->executeAction($categoryFilter);
            }
            $this->messageManager->addSuccessMessage(__($this->successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __($this->errorMessage));
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('mageworx_seoextended/*/index');

        return $redirectResult;
    }
}
