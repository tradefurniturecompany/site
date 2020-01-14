<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templateproduct;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\SeoXTemplates\Controller\Adminhtml\Templateproduct;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\ProductFactory as TemplateProductFactory;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\SeoXTemplates\Model\ResourceModel\Template\Product\CollectionFactory;
use MageWorx\SeoXTemplates\Model\Template\Product as TemplateProductModel;

abstract class MassAction extends Templateproduct
{
    /**
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     *
     * @var \MageWorx\SeoXTemplates\Model\ResourceModel\Template\Product\CollectionFactory
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
     * @param TemplateProductFactory $templateProductFactory
     * @param Context $context
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Filter $filter,
        Registry $registry,
        TemplateProductFactory $templateProductFactory,
        Context $context
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        parent::__construct($registry, $templateProductFactory, $context);
    }

    /**
     *
     * @param TemplateProductModel $template
     * @return mixed
     */
    abstract protected function doTheAction(TemplateProductModel $template);

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
