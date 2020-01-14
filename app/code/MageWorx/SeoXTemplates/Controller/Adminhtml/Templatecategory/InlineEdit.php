<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategory;

use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\CategoryFactory as TemplateCategoryFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategory as TemplatecategoryController;
use Magento\Framework\Registry;
use MageWorx\SeoXTemplates\Model\Template\Category as TemplateCategory;

class InlineEdit extends TemplatecategoryController
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     *
     * @param JsonFactory $jsonFactory
     * @param Registry $registry
     * @param TemplateCategoryFactory $templateCategoryFactory
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        Registry $registry,
        TemplateCategoryFactory $templateCategoryFactory,
        Context $context
    ) {
        $this->jsonFactory = $jsonFactory;
        parent::__construct($registry, $templateCategoryFactory, $context);
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
            /** @var \MageWorx\SeoXTemplates\Model\TemplateCategory $template */
            $template = $this->templateCategoryFactory->create()->load($templateId);
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
                    __('Something went wrong while saving the category template.')
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
     * @param TemplateCategory $template
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithTemplateId(TemplateCategory $template, $errorText)
    {
        return '[Template ID: ' . $template->getId() . '] ' . $errorText;
    }
}
