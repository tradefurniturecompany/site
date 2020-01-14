<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategoryfilter;

use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\CategoryFilterFactory as TemplateCategoryFilterFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategoryfilter as TemplatecategoryfilterController;
use Magento\Framework\Registry;
use MageWorx\SeoXTemplates\Model\Template\CategoryFilter as TemplateCategoryFilter;

class InlineEdit extends TemplatecategoryfilterController
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     *
     * @param JsonFactory $jsonFactory
     * @param Registry $registry
     * @param TemplateCategoryFilterFactory $templateCategoryFilterFactory
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        Registry $registry,
        TemplateCategoryFilterFactory $templateCategoryFilterFactory,
        Context $context
    ) {
        $this->jsonFactory = $jsonFactory;
        parent::__construct($registry, $templateCategoryFilterFactory, $context);
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
            /** @var TemplateCategoryFilter $template */
            $template = $this->templateCategoryFilterFactory->create()->load($templateId);
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
                    __('Something went wrong while saving the category filter template.')
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
     * @param TemplateCategoryFilter $template
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithTemplateId(TemplateCategoryFilter $template, $errorText)
    {
        return '[Template ID: ' . $template->getId() . '] ' . $errorText;
    }
}
