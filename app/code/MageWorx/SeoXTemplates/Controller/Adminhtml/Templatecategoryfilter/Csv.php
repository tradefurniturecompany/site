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
use MageWorx\SeoXTemplates\Model\CsvWriterCategoryFilterFactory;
use MageWorx\SeoXTemplates\Helper\Data as HelperData;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\SeoXTemplates\Helper\CategoryFilterGenerator;
use MageWorx\SeoXTemplates\Model\Template\CategoryFilter;

class Csv extends TemplateController
{
    /**
     * @var CsvWriterCategoryFilterFactory
     */
    protected $csvWriterCategoryFlterFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperStore
     */
    protected $helperStore;

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
     * @var CategoryFilterGenerator
     */
    protected $categoryFilterGenerator;

    /**
     *
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param TemplateCategoryFilterFactory $templateCategoryFilterFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        TemplateCategoryFilterFactory $templateCategoryFilterFactory,
        CsvWriterCategoryFilterFactory $csvWriterCategoryFilterFactory,
        HelperData $helperData,
        HelperStore $helperStore,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        CategoryFilterGenerator $categoryFilterGenerator,
        Context $context
    ) {
        $this->csvWriterCategoryFlterFactory = $csvWriterCategoryFilterFactory;
        $this->helperData              = $helperData;
        $this->helperStore             = $helperStore;
        $this->resultPageFactory       = $resultPageFactory;
        $this->resultRawFactory        = $resultRawFactory;
        $this->fileFactory             = $fileFactory;
        $this->categoryFilterGenerator = $categoryFilterGenerator;
        parent::__construct($registry, $templateCategoryFilterFactory, $context);
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
                /** @var \MageWorx\SeoXTemplates\Model\Template\CategoryFilter $template */
                $template = $this->templateCategoryFilterFactory->create();
                $template->load($id);

                $temporaryCategoryFiltersIds = [];

                if ($template->getStoreId() == 0) {
                    $content  = null;
                    $storeIds = array_keys($this->helperStore->getActiveStores());
                    foreach ($storeIds as $storeId) {
                        $temporaryCategoryFiltersIds = array_merge(
                            $temporaryCategoryFiltersIds,
                            $this->categoryFilterGenerator->createMissingSeoFilters($template, $storeId)
                        );
                        $content  = $this->writeTemplateForStore($template, $content, $storeId);
                    }
                } else {
                    $temporaryCategoryFiltersIds = $this->categoryFilterGenerator->createMissingSeoFilters($template);
                    $content = $this->writeTemplateForStore($template);
                }

                $templateCategoryFilter = $this->templateCategoryFilterFactory->create();
                $categoryFilterCollectionFactory = $templateCategoryFilter->getCategoryFilterCollectionFactory();

                if ($categoryFilterCollectionFactory && $temporaryCategoryFiltersIds) {

                    $categoryFilterCollection = $categoryFilterCollectionFactory->create();
                    $categoryFilterCollection->addIdsToFilter($temporaryCategoryFiltersIds);
                    $categoryFilterCollection->walk('delete');
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
     * @param \MageWorx\SeoXTemplates\Model\Template\CategoryFilter $template
     * @param array|null $content
     * @param int|null $nestedStoreId
     * @return array
     */
    protected function writeTemplateForStore($template, $content = null, $nestedStoreId = null)
    {
        $from      = 0;
        $limit     = $this->helperData->getTemplateLimitForCurrentStore();

        /** @var \Magento\SeoXTemplates\Model\CsvWriterCategoryFilter $csvWriter */
        $csvWriter = $this->csvWriterCategoryFlterFactory->create($template->getTypeId());
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
