<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatelandingpage;

use Magento\Framework\Registry;
use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatelandingpage as TemplateController;
use MageWorx\SeoXTemplates\Model\Template\LandingPageFactory as TemplateLandingPageFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Backend\App\Action\Context;

class Save extends TemplateController
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     *
     * @param DateTime $date
     * @param JsHelper $jsHelper
     * @param Registry $registry
     * @param TemplateLandingPageFactory $templateLandingPageFactory
     * @param Context $context
     */
    public function __construct(
        DateTime $date,
        JsHelper $jsHelper,
        Registry $registry,
        TemplateLandingPageFactory $templateLandingPageFactory,
        Context $context
    ) {
        $this->date     = $date;
        $this->jsHelper = $jsHelper;
        parent::__construct($registry, $templateLandingPageFactory, $context);
    }

    /**
     * Save the Landing Page template
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('template');

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->filterData($data);

            if (!empty($this->getRequest()->getParam('template')['template_id'])) {
                $template = $this->initTemplateLandingPage($this->getRequest()->getParam('template')['template_id']);
            } else {
                $template = $this->initTemplateLandingPage();
            }

            $template->setData($data);
            $landingPages = $this->getRequest()->getPost('landingpages', -1);
            if ($landingPages != -1) {
                $template->setLandingPagesData($this->jsHelper->decodeGridSerializedInput($landingPages));
            }

            $this->_eventManager->dispatch(
                'mageworx_seoxtemplates_template_landingpage_prepare_save',
                [
                    'template' => $template,
                    'request'  => $this->getRequest()
                ]
            );

            try {
                $template->setDateModified($this->date->gmtDate());
                $template->save();
                $this->_getSession()->setMageworxSeoXTemplatesTemplateLandingPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mageworx_seoxtemplates/*/edit',
                        [
                            'template_id' => $template->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('mageworx_seoxtemplates/*/');
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the template.'));
            }

            $this->_getSession()->setMageworxSeoXTemplatesTemplateLandingPageData($data);
            $resultRedirect->setPath(
                'mageworx_seoxtemplates/*/edit',
                [
                    'template_id' => $template->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
        $resultRedirect->setPath('mageworx_seoxtemplates/*/');
        return $resultRedirect;
    }
}
