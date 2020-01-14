<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templateproduct;

use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\ProductFactory as TemplateProductFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\SeoXTemplates\Controller\Adminhtml\Templateproduct as TemplateproductController;
use Magento\Framework\Registry;
use MageWorx\SeoXTemplates\Model\Template\Product as TemplateProduct;

class InlineEdit extends TemplateproductController
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     *
     * @param JsonFactory $jsonFactory
     * @param Registry $registry
     * @param TemplateProductFactory $templateProductFactory
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        Registry $registry,
        TemplateProductFactory $templateProductFactory,
        Context $context
    ) {
        $this->jsonFactory = $jsonFactory;
        parent::__construct($registry, $templateProductFactory, $context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $templateId) {
            /** @var \MageWorx\SeoXTemplates\Model\TemplateProduct $template */
            $template = $this->templateProductFactory->create()->load($templateId);
            try {
                $templateData = $this->filterData($postItems[$templateId]);
                $template->addData($templateData);

                $template->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithTemplateId($template, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithTemplateId($template, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithTemplateId(
                    $template,
                    __('Something went wrong while saving the product template.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add template id to error message
     *
     * @param TemplateProduct $template
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithTemplateId(TemplateProduct $template, $errorText)
    {
        return '[Template ID: ' . $template->getId() . '] ' . $errorText;
    }
}
