<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategoryfilter;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategoryfilter;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\CategoryFilterFactory as TemplateCategoryFilterFactory;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\SeoXTemplates\Model\ResourceModel\Template\CategoryFilter\CollectionFactory;
use MageWorx\SeoXTemplates\Model\Template\CategoryFilter as TemplateCategoryFilterModel;

abstract class MassAction extends Templatecategoryfilter
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\ResourceModel\Template\CategoryFilter\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var string
     */
    protected $successMessage = 'Mass Action successful on %1 records';

    /**
     * @var string
     */
    protected $errorMessage = 'Mass Action failed';

    /**
     *
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param Registry $registry
     * @param TemplateCategoryFilterFactory $templateCategoryFilterFactory
     * @param Context $context
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Filter $filter,
        Registry $registry,
        TemplateCategoryFilterFactory $templateCategoryFilterFactory,
        Context $context
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        parent::__construct($registry, $templateCategoryFilterFactory, $context);
    }

    /**
     *
     * @param TemplateCategoryFilterModel $template
     * @return mixed
     */
    abstract protected function doTheAction(TemplateCategoryFilterModel $template);

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $template) {
                $this->doTheAction($template);
            }
            $this->messageManager->addSuccess(__($this->successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __($this->errorMessage));
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('mageworx_seoxtemplates/*/index');
        return $redirectResult;
    }
}
