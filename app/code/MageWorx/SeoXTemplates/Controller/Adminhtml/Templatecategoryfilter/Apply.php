<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategoryfilter;

use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategoryfilter as TemplateController;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\CategoryFilterFactory as TemplateCategoryFilterFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MageWorx\SeoXTemplates\Model\DbWriterCategoryFilterFactory;
use MageWorx\SeoXTemplates\Helper\Data as HelperData;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\SeoXTemplates\Helper\CategoryFilterGenerator;

class Apply extends TemplateController
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var DbWriterCategoryFilterFactory
     */
    protected $dbWriterCategoryFilterFactory;

    /**
     *
     * @var PageFactory
     */
    protected $resultPageFactor;

    /**
     *
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperStore
     */
    protected $helperStore;

    /**
     * @var CategoryFilterGenerator
     */
    protected $categoryFilterGenerator;

    /**
     *
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param TemplateCategoryFilterFactory $templateCategoryFilterFactory
     * @param DateTime $date
     * @param DbWriterCategoryFilterFactory $dbWriterCategoryFilterFactory
     * @param HelperData $helperData
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        TemplateCategoryFilterFactory $templateCategoryFilterFactory,
        DateTime $date,
        DbWriterCategoryFilterFactory $dbWriterCategoryFilterFactory,
        HelperStore $helperStore,
        HelperData $helperData,
        CategoryFilterGenerator $categoryFilterGenerator,
        Context $context
    ) {
    
        $this->date = $date;
        $this->dbWriterCategoryFilterFactory = $dbWriterCategoryFilterFactory;
        $this->helperStore = $helperStore;
        $this->helperData = $helperData;
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryFilterGenerator = $categoryFilterGenerator;
        parent::__construct($registry, $templateCategoryFilterFactory, $context);
    }

    /**
     * Apply template
     *
     * @param \MageWorx\SeoXTemplates\Model\Template\CategoryFilter $template
     * @param int $nestedStoreId
     */
    protected function writeTemplateForStore($template, $nestedStoreId = null)
    {
        $from      = 0;
        $limit     = $this->helperData->getTemplateLimitForCurrentStore();
        $dbWriter = $this->dbWriterCategoryFilterFactory->create($template->getTypeId());
        $categoryCollection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);

        while (is_object($categoryCollection) && $categoryCollection->count() > 0) {
            $dbWriter->write($categoryCollection, $template, $nestedStoreId);
            $from += $limit;
            $categoryCollection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);
        }
    }

    /**
     * Write category filter template
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('template_id');

        if ($id) {
            $name = "";
            try {
                /** @var \MageWorx\SeoXTemplates\Model\Template\CategoryFilter $template */
                $template = $this->templateCategoryFilterFactory->create();
                $template->load($id);
                $template->setDateApplyStart($this->date->gmtDate());
                $name = $template->getName();

                if ($template->getStoreId() == 0) {
                    $storeIds = array_keys($this->helperStore->getActiveStores());
                    foreach ($storeIds as $storeId) {
                        $this->categoryFilterGenerator->createMissingSeoFilters($template, $storeId);
                        $this->writeTemplateForStore($template, $storeId);
                    }
                } else {
                    $this->categoryFilterGenerator->createMissingSeoFilters($template);
                    $this->writeTemplateForStore($template);
                }

                $this->messageManager->addSuccess(__('Template "%1" has been applied.', $name));
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_seoxtemplates_template_categoryfilter_on_apply',
                    ['name' => $name, 'status' => 'success']
                );
                $resultRedirect->setPath('mageworx_seoxtemplates/*/');

                $template->setDateApplyFinish($this->date->gmtDate());
                $template->save();

                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_seoxtemplates_template_categoryfilter_on_apply',
                    ['name' => $name, 'status' => 'fail']
                );
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setPath('mageworx_seoxtemplates/*/index', ['template_id' => $id]);
                return $resultRedirect;
            }
        }
        $this->messageManager->addError(__('We can\'t find a category filter  template to apply.'));
        $resultRedirect->setPath('mageworx_seoxtemplates/*/');
        return $resultRedirect;
    }
}
