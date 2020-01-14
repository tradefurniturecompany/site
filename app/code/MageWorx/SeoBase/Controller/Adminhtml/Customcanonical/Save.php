<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Controller\Adminhtml\Customcanonical;

use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\InputException;
use MageWorx\SeoBase\Controller\Adminhtml\Customcanonical;
use MageWorx\SeoBase\Model\CustomCanonical as CustomCanonicalModel;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ResponseInterface;

class Save extends Customcanonical
{
    /**
     * @return ResponseInterface|ResultRedirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getParam(CustomCanonicalModel::CUSTOM_CANONICAL_FORM_DATA_KEY);

        if ($data) {
            $data     = $this->prepareData($data);
            $entityId = !empty($data[CustomCanonicalModel::ENTITY_ID]) ? $data[CustomCanonicalModel::ENTITY_ID] : null;
            $isEdit   = (bool)$entityId;

            try {
                if ($entityId) {
                    /** @var CustomCanonicalModel $customCanonical */
                    $customCanonical = $this->customCanonicalRepository->getById($entityId);
                } else {
                    $customCanonical = $this->customCanonicalRepository->getEmptyEntity();
                }

                $customCanonical->setData($data);

                $sourceStores = $this->getCustomCanonicalSourceStores($data);

                foreach ($sourceStores as $sourceStoreId) {
                    $successFlag = true;
                    $storeName   = $this->storeManager->getStore($sourceStoreId)->getName();

                    $customCanonical->setSourceStoreId($sourceStoreId);

                    if ($this->helperCustomCanonical->isRecursiveCustomCanonical($customCanonical)) {
                        $message = __('It is impossible to save Custom Canonical for %1 Store', $storeName)
                            . ': '
                            . __('Source and target entities can\'t be identical!');
                        $this->messageManager->addNoticeMessage($message);
                        continue;
                    }

                    if (!$isEdit) {
                        $customCanonical->setId($entityId);
                    }

                    $this->_eventManager->dispatch(
                        CustomCanonicalModel::CURRENT_CUSTOM_CANONICAL . '_prepare_save',
                        [
                            'custom_canonical' => $customCanonical,
                            'request'          => $this->getRequest()
                        ]
                    );

                    try {
                        $this->customCanonicalRepository->save($customCanonical);
                    } catch (LocalizedException $e) {
                        $successFlag      = false;
                        $exceptionMessage = $e->getMessage();

                        if (count($sourceStores) > 1) {
                            $exceptionMessage = __('[%1 Store]', $storeName) . ' - ' . $exceptionMessage;
                        }

                        $this->messageManager->addErrorMessage($exceptionMessage);
                        $this->logger->critical($e);
                    }

                    if ($successFlag) {

                        if (count($sourceStores) > 1) {
                            $successMessage = __('The Custom Canonical for %1 Store has been saved.', $storeName);
                        } else {
                            $successMessage = __('The Custom Canonical has been saved.');
                        }
                        $this->messageManager->addSuccessMessage($successMessage);
                    }

                    if ($isEdit) {
                        break;
                    }
                }
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (InputException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the Custom Canonical.')
                );
                $this->logger->critical($e);
            }
        }

        $resultRedirect->setPath('*/*/index');

        return $resultRedirect;
    }

    /**
     * @param array $data
     * @return array
     */
    private function prepareData($data)
    {
        $sourceChooserTypeOptions = $this->helperCustomCanonical->getSourceChooserTypeOptions();
        $targetChooserTypeOptions = $this->helperCustomCanonical->getTargetChooserTypeOptions();

        $sourceEntityType = $data[CustomCanonicalModel::SOURCE_ENTITY_TYPE];
        $sourceEntityId   = $this->helperCustomCanonical->cropChooserValuePrefix(
            $data[$sourceChooserTypeOptions[$sourceEntityType]],
            $sourceEntityType
        );

        $data[CustomCanonicalModel::SOURCE_ENTITY_ID] = $sourceEntityId;

        $targetEntityType = $data[CustomCanonicalModel::TARGET_ENTITY_TYPE];

        if ($targetEntityType != Rewrite::ENTITY_TYPE_CUSTOM) {
            $targetEntityId = $this->helperCustomCanonical->cropChooserValuePrefix(
                $data[$targetChooserTypeOptions[$targetEntityType]],
                $targetEntityType
            );

            $data[CustomCanonicalModel::TARGET_ENTITY_ID] = $targetEntityId;
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    private function getCustomCanonicalSourceStores($data)
    {
        $storeIds = [];

        if (!empty($data['source_stores'])) {
            $stores = $data['source_stores'];

            if (in_array(strval(\Magento\Store\Model\Store::DEFAULT_STORE_ID), $stores, true)) {
                $storeIds[] = strval(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
            } else {
                $storeIds = $stores;
            }
        } else {
            $storeIds[] = $data[CustomCanonicalModel::SOURCE_STORE_ID];
        }

        return $storeIds;
    }
}
