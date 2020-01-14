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
use MageWorx\SeoXTemplates\Model\CsvWriterCategoryFactory;
use MageWorx\SeoXTemplates\Helper\Data as HelperData;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\SeoXTemplates\Controller\Adminhtml\Validator\Helper as TemplateValidator;

class Csv extends TemplateController
{
    /**
     * @var CsvWriterCategoryFactory
     */
    protected $csvWriterCategoryFactory;

    /**
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
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     *
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param TemplateCategoryFactory $templateCategoryFactory
     * @param Context $context
     * @param TemplateValidator $templateValidator
     */
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        TemplateCategoryFactory $templateCategoryFactory,
        CsvWriterCategoryFactory $csvWriterCategoryFactory,
        HelperData $helperData,
        HelperStore $helperStore,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        Context $context,
        TemplateValidator $templateValidator
    ) {
    
        $this->csvWriterCategoryFactory = $csvWriterCategoryFactory;
        $this->helperData              = $helperData;
        $this->helperStore             = $helperStore;
        $this->resultPageFactory       = $resultPageFactory;
        $this->resultRawFactory        = $resultRawFactory;
        $this->fileFactory             = $fileFactory;
        $this->templateValidator       = $templateValidator;
        parent::__construct($registry, $templateCategoryFactory, $context);
    }

    /**
     * Retrieve CSV file
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('template_id');

        if ($id) {
            try {
                /** @var \MageWorx\SeoXTemplate\Model\Template\Category $template */
                $template = $this->templateCategoryFactory->create();
                $template->load($id);

                if (!$this->templateValidator->validate($template)->isValidByStoreMode()) {

                    $resultRedirect->setPath('mageworx_seoxtemplates/*/');
                    return $resultRedirect;
                }

                if ($template->getStoreId() == 0 && !$template->getIsSingleStoreMode()) {
                    $content  = null;
                    $storeIds = array_keys($this->helperStore->getActiveStores());
                    foreach ($storeIds as $storeId) {
                        $content  = $this->writeTemplateForStore($template, $content, $storeId);
                    }
                } else {
                    $content = $this->writeTemplateForStore($template);
                }

                return $this->fileFactory->create(
                    'seoxtemplates.csv',
                    $content,
                    \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setPath('mageworx_seoxtemplates/*/index', ['template_id' => $id]);
                return $resultRedirect;
            }
        }
        $this->messageManager->addError(__('We can\'t find a category template to apply.'));
        $resultRedirect->setPath('mageworx_seoxtemplates/*/');
        return $resultRedirect;
    }

    /**
     *
     * @param \MageWorx\SeoXTemplates\Model\Template\Category $template
     * @param array|null $content
     * @param int|null $nestedStoreId
     * @return array
     */
    protected function writeTemplateForStore($template, $content = null, $nestedStoreId = null)
    {
        $from      = 0;
        $limit     = $this->helperData->getTemplateLimitForCurrentStore();
        $csvWriter = $this->csvWriterCategoryFactory->create($template->getTypeId());

        $categoryCollection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);

        while (is_object($categoryCollection) && $categoryCollection->count() > 0) {
            $filename = is_null($content) ? null : $content['value'];
            $content  = $csvWriter->write($categoryCollection, $template, $filename, $nestedStoreId);

            $from += $limit;

            $categoryCollection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);
        }

        return $content;
    }
}
