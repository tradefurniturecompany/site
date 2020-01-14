<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatelandingpage;

use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatelandingpage as TemplateController;
use Magento\Framework\Registry;
use MageWorx\SeoXTemplates\Model\Template\LandingPageFactory as TemplateLandingPageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\LayoutFactory;

class Landingpages extends TemplateController
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     *
     * @param LayoutFactory $resultLayoutFactory
     * @param Registry $registry
     * @param TemplateLandingPageFactory $templateLandingPageFactory
     * @param Context $context
     */
    public function __construct(
        LayoutFactory $resultLayoutFactory,
        Registry $registry,
        TemplateLandingPageFactory $templateLandingPageFactory,
        Context $context
    ) {
    
        $this->resultLayoutFactory = $resultLayoutFactory;
        parent::__construct($registry, $templateLandingPageFactory, $context);
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->initTemplateLandingPage();
        $resultLayout = $this->resultLayoutFactory->create();
        /** @var \MageWorx\SeoXTemplates\Block\Adminhtml\Template\Landingpage\Edit\Tab\Landingpages $landingPageBlock */
        $landingPageBlock = $resultLayout->getLayout()->getBlock('template_edit_tab_landingpage');
        if ($landingPageBlock) {
            $landingPageBlock->setTemplateProducts($this->getRequest()->getPost('template_landingpages', null));
        }
        return $resultLayout;
    }
}
