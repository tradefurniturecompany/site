<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategory;

use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategory as TemplateController;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\CategoryFactory as TemplateCategoryFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MageWorx\SeoXTemplates\Model\DbWriterCategoryFactory;
use MageWorx\SeoXTemplates\Helper\Data as HelperData;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\SeoXTemplates\Controller\Adminhtml\Validator\Helper as TemplateValidator;
use MageWorx\SeoXTemplates\Model\AbstractTemplate;

class Apply extends TemplateController
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var DbWriterCategoryFactory
     */
    protected $dbWriterCategoryFactory;

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
     * @var TemplateValidator
     */
    protected $templateValidator;

    /**
     *
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param TemplateCategoryFactory $templateCategoryFactory
     * @param DateTime $date
     * @param DbWriterCategoryFactory $dbWriterCategoryFactory
     * @param HelperData $helperData
     * @param Context $context
     * @param TemplateValidator $templateValidator
     */
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        TemplateCategoryFactory $templateCategoryFactory,
        DateTime $date,
        DbWriterCategoryFactory $dbWriterCategoryFactory,
        HelperStore $helperStore,
        HelperData $helperData,
        Context $context,
        TemplateValidator $templateValidator
    ) {
    
        $this->date = $date;
        $this->dbWriterCategoryFactory = $dbWriterCategoryFactory;
        $this->helperStore = $helperStore;
        $this->helperData = $helperData;
        $this->resultPageFactory = $resultPageFactory;
        $this->templateValidator = $templateValidator;
        parent::__construct($registry, $templateCategoryFactory, $context);
    }

    /**
     * Apply template
     *
     * @param \MageWorx\SeoXTemplates\Model\Template\Category $template
     * @param int $nestedStoreId
     */
    protected function writeTemplateForStore($template, $nestedStoreId = null)
    {
        $from      = 0;
        $limit     = $this->helperData->getTemplateLimitForCurrentStore();
        $dbWriter = $this->dbWriterCategoryFactory->create($template->getTypeId());

        $categoryCollection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);

        while (is_object($categoryCollection) && $categoryCollection->count() > 0) {
            $dbWriter->write($categoryCollection, $template, $nestedStoreId);

            if ($template->getScope() != AbstractTemplate::SCOPE_EMPTY) {
                $from += $limit;
            }

            $categoryCollection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);
        }
    }

    /**
     * Write category template
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
                /** @var \MageWorx\SeoXTemplates\Model\Template\Category $template */
                $template = $this->templateCategoryFactory->create();
                $template->load($id);

                if (!$this->templateValidator->validate($template)->isValidByStoreMode()) {

                    $resultRedirect->setPath('mageworx_seoxtemplates/*/');
                    return $resultRedirect;
                }

                $template->setDateApplyStart($this->date->gmtDate());
                $name = $template->getName();

                if ($template->getStoreId() == 0 && !$template->getIsSingleStoreMode()) {
                    $storeIds = array_keys($this->helperStore->getActiveStores());
                    foreach ($storeIds as $storeId) {
                        $this->writeTemplateForStore($template, $storeId);
                    }
                } else {
                    $this->writeTemplateForStore($template);
                }

                $this->messageManager->addSuccess(__('Template "%1" has been applied.', $name));
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_seoxtemplates_template_category_on_apply',
                    ['name' => $name, 'status' => 'success']
                );
                $resultRedirect->setPath('mageworx_seoxtemplates/*/');

                $template->setDateApplyFinish($this->date->gmtDate());
                $template->save();

                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_seoxtemplates_template_category_on_apply',
                    ['name' => $name, 'status' => 'fail']
                );
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setPath('mageworx_seoxtemplates/*/index', ['template_id' => $id]);
                return $resultRedirect;
            }
        }
        $this->messageManager->addError(__('We can\'t find a category template to apply.'));
        $resultRedirect->setPath('mageworx_seoxtemplates/*/');
        return $resultRedirect;
    }
}
