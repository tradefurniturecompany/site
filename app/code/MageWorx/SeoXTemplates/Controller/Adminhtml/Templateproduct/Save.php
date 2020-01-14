<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templateproduct;

use Magento\Framework\Registry;
use MageWorx\SeoXTemplates\Controller\Adminhtml\Templateproduct as TemplateProductController;
use MageWorx\SeoXTemplates\Model\Template\ProductFactory as TemplateProductFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Backend\App\Action\Context;

class Save extends TemplateProductController
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
     * @param TemplateProductFactory $templateProductFactory
     * @param Context $context
     */
    public function __construct(
        DateTime $date,
        JsHelper $jsHelper,
        Registry $registry,
        TemplateProductFactory $templateProductFactory,
        Context $context
    ) {
    
        $this->date     = $date;
        $this->jsHelper = $jsHelper;
        parent::__construct($registry, $templateProductFactory, $context);
    }

    /**
     * run the action
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
                $template = $this->initTemplateProduct($this->getRequest()->getParam('template')['template_id']);
            } else {
                $template = $this->initTemplateProduct();
            }

            $template->setData($data);
            $products = $this->getRequest()->getPost('products', -1);

            if ($products != -1) {
                $template->setProductsData($this->jsHelper->decodeGridSerializedInput($products));
            }

            $this->_eventManager->dispatch(
                'mageworx_seoxtemplates_template_product_prepare_save',
                [
                    'template' => $template,
                    'request'  => $this->getRequest()
                ]
            );

            try {
                $template->setDateModified($this->date->gmtDate());
                $template->save();
                $this->_getSession()->setMageworxSeoXTemplatesTemplateProductData(false);
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

            $this->_getSession()->setMageworxSeoXTemplatesTemplateProductData($data);
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
