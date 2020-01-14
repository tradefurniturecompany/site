<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatelandingpage;

use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatelandingpage as TemplateController;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\LandingPageFactory as TemplateLandingPageFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MageWorx\SeoXTemplates\Model\DbWriterLandingPageFactory;
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
     * @var DbWriterLandingPageFactory
     */
    protected $dbWriterLandingPageFactory;

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
     * @param TemplateLandingPageFactory $templateLandingPageFactory
     * @param DateTime $date
     * @param DbWriterLandingPageFactory $dbWriterLandingPageFactory
     * @param HelperData $helperData
     * @param Context $context
     * @param TemplateValidator $templateValidator
     */
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        TemplateLandingPageFactory $templateLandingPageFactory,
        DateTime $date,
        DbWriterLandingPageFactory $dbWriterLandingPageFactory,
        HelperStore $helperStore,
        HelperData $helperData,
        Context $context,
        TemplateValidator $templateValidator
    ) {
    
        $this->date = $date;
        $this->dbWriterLandingPageFactory = $dbWriterLandingPageFactory;
        $this->helperStore = $helperStore;
        $this->helperData = $helperData;
        $this->resultPageFactory = $resultPageFactory;
        $this->templateValidator = $templateValidator;
        parent::__construct($registry, $templateLandingPageFactory, $context);
    }

    /**
     * Apply template
     *
     * @param \MageWorx\SeoXTemplates\Model\Template\LandingPage $template
     * @param int $nestedStoreId
     */
    protected function writeTemplateForStore($template, $nestedStoreId = null)
    {
        $from      = 0;
        $limit     = $this->helperData->getTemplateLimitForCurrentStore();
        $dbWriter = $this->dbWriterLandingPageFactory->create($template->getTypeId());

        $landingPageCollection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);

        while (is_object($landingPageCollection) && $landingPageCollection->count() > 0) {
            $dbWriter->write($landingPageCollection, $template, $nestedStoreId);

            if ($template->getScope() != AbstractTemplate::SCOPE_EMPTY) {
                $from += $limit;
            }
            $landingPageCollection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);
        }
    }

    /**
     * Write landing Page template
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
                /** @var \MageWorx\SeoXTemplates\Model\Template\LandingPage $template */
                $template = $this->templateLandingPageFactory->create();
                $template->load($id);

                if (!$this->templateValidator->validate($template)->isValidByStoreMode()) {

                    $resultRedirect->setPath('mageworx_seoxtemplates/*/');
                    return $resultRedirect;
                }

                $template->setDateApplyStart($this->date->gmtDate());
                $name = $template->getName();

                if ($template->getStoreId() == 0
                    && !$template->getIsSingleStoreMode()
                    && !$template->getUseForDefaultValue())
                {
                    $storeIds = array_keys($this->helperStore->getActiveStores());
                    foreach ($storeIds as $storeId) {
                        $this->writeTemplateForStore($template, $storeId);
                    }
                } else {
                    $this->writeTemplateForStore($template);
                }

                $this->messageManager->addSuccess(__('Template "%1" has been applied.', $name));
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_seoxtemplates_template_landingpage_on_apply',
                    ['name' => $name, 'status' => 'success']
                );
                $resultRedirect->setPath('mageworx_seoxtemplates/*/');

                $template->setDateApplyFinish($this->date->gmtDate());
                $template->save();

                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_seoxtemplates_template_landingpage_on_apply',
                    ['name' => $name, 'status' => 'fail']
                );
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setPath('mageworx_seoxtemplates/*/index', ['template_id' => $id]);
                return $resultRedirect;
            }
        }
        $this->messageManager->addError(__('We can\'t find a landing page template to apply.'));
        $resultRedirect->setPath('mageworx_seoxtemplates/*/');
        return $resultRedirect;
    }
}
