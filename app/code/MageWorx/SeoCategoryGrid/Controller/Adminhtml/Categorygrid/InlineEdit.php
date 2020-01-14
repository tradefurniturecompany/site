<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCategoryGrid\Controller\Adminhtml\Categorygrid;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\DataObject;
use MageWorx\SeoCategoryGrid\Controller\Adminhtml\Categorygrid;
use MageWorx\SeoAll\Model\ResourceModel\Category as CategoryResource;


class InlineEdit extends Categorygrid
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var CategoryResource
     */
    protected $categoryResource;

    /**
     * InlineEdit constructor.
     *
     * @param JsonFactory $jsonFactory
     * @param CategoryResource $categoryResource
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        CategoryResource $categoryResource,
        Context $context
    ) {
        $this->jsonFactory      = $jsonFactory;
        $this->categoryResource = $categoryResource;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $itemsData = $this->getRequest()->getParam('items');
        $storeId   = (int)$this->getRequest()->getParam('store_id');

        try {
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->jsonFactory->create();
            $error      = false;
            $messages   = [];

            if (!($this->getRequest()->getParam('isAjax') || !count($itemsData))) {
                return $resultJson->setData(
                    [
                        'messages' => [__('Please correct the data sent.')],
                        'error'    => true,
                    ]
                );
            }

            $categoryData = new DataObject();

            $categoryData->setData('store_id', $storeId);

            foreach ($itemsData as $itemData) {
                $itemId = $itemData['entity_id'];
                unset($itemData['entity_id']);

                $categoryData->setId($itemId);

                $entityId = $this->categoryResource->resolveEntityId($itemId);
                $categoryData->setData($this->categoryResource->getLinkField(), $entityId);

                foreach ($itemData as $attributeCode => $attributeValue) {
                    $categoryData->setData($attributeCode, $attributeValue);

                    try {
                        $this->categoryResource->saveAttribute($categoryData, $attributeCode);
                        unset($categoryData[$attributeCode]);
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $messages[] = $this->getError($categoryData->getId(), $attributeCode, $e->getMessage());
                        $error      = true;
                    } catch (\RuntimeException $e) {
                        $messages[] = $this->getError($categoryData->getId(), $attributeCode, $e->getMessage());
                        $error      = true;
                    } catch (\Exception $e) {
                        $messages[] = $this->getError(
                            $categoryData->getId(),
                            $attributeCode,
                            __('Something went wrong while saving the category attribute data.')
                        );
                        $error      = true;
                    }
                }
            }
        } catch (\Exception $e) {
            $messages[] = $e->getMessage();
            $error      = true;
        } catch (\Error $e) {
            $messages[] = $e->getMessage();
            $error      = true;
        }

        return $resultJson->setData(
            [
                'messages' => $messages,
                'error'    => $error
            ]
        );
    }

    /**
     * Add category id and attribute code to error message
     *
     * @param int|string $categoryId
     * @param string $attributeCode
     * @param string $errorText
     * @return string
     */
    protected function getError($categoryId, $attributeCode, $errorText)
    {
        return '[Category ID: ' . $categoryId . ', ' . 'Attribute Code: ' . $attributeCode . '] ' . $errorText;
    }
}
