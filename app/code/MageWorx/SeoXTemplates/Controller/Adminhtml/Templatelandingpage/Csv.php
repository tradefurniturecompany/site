<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatelandingpage;

use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatelandingpage as TemplateController;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\LandingPageFactory as TemplateLandingPageFactory;
use MageWorx\SeoXTemplates\Model\CsvWriterLandingPageFactory;
use MageWorx\SeoXTemplates\Helper\Data as HelperData;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\SeoXTemplates\Controller\Adminhtml\Validator\Helper as TemplateValidator;

class Csv extends TemplateController
{
    /**
     * @var CsvWriterLandingPageFactory
     */
    protected $csvWriterLandingPageFactory;

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
     * Csv constructor.
     *
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param TemplateLandingPageFactory $templateLandingPageFactory
     * @param CsvWriterLandingPageFactory $csvWriterLandingPageFactory
     * @param HelperData $helperData
     * @param HelperStore $helperStore
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param Context $context
     * @param TemplateValidator $templateValidator
     */
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        TemplateLandingPageFactory $templateLandingPageFactory,
        CsvWriterLandingPageFactory $csvWriterLandingPageFactory,
        HelperData $helperData,
        HelperStore $helperStore,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        Context $context,
        TemplateValidator $templateValidator
    ) {
    
        $this->csvWriterLandingPageFactory = $csvWriterLandingPageFactory;
        $this->helperData              = $helperData;
        $this->helperStore             = $helperStore;
        $this->resultPageFactory       = $resultPageFactory;
        $this->resultRawFactory        = $resultRawFactory;
        $this->fileFactory             = $fileFactory;
        $this->templateValidator       = $templateValidator;
        parent::__construct($registry, $templateLandingPageFactory, $context);
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
                /** @var \MageWorx\SeoXTemplate\Model\Template\LandingPage $template */
                $template = $this->templateLandingPageFactory->create();
                $template->load($id);

                if (!$this->templateValidator->validate($template)->isValidByStoreMode()) {

                    $resultRedirect->setPath('mageworx_seoxtemplates/*/');
                    return $resultRedirect;
                }

                if ($template->getStoreId() == 0
                    && !$template->getIsSingleStoreMode()
                    && !$template->getUseForDefaultValue()
                ) {
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
        $this->messageManager->addError(__('We can\'t find a landing page template to apply.'));
        $resultRedirect->setPath('mageworx_seoxtemplates/*/');
        return $resultRedirect;
    }

    /**
     *
     * @param \MageWorx\SeoXTemplates\Model\Template\LandingPage $template
     * @param array|null $content
     * @param int|null $nestedStoreId
     * @return array
     */
    protected function writeTemplateForStore($template, $content = null, $nestedStoreId = null)
    {
        $from      = 0;
        $limit     = $this->helperData->getTemplateLimitForCurrentStore();
        $csvWriter = $this->csvWriterLandingPageFactory->create($template->getTypeId());

        $landingPageCollection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);
        $landingPageCollection->count();

        while (is_object($landingPageCollection) && $landingPageCollection->count() > 0) {
            $filename = is_null($content) ? null : $content['value'];
            $content  = $csvWriter->write($landingPageCollection, $template, $filename, $nestedStoreId);

            $from += $limit;

            $landingPageCollection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);
        }

        return $content;
    }
}
