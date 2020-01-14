<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Controller\Adminhtml\Crosslink;

use MageWorx\SeoCrossLinks\Controller\Adminhtml\Crosslink;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\SeoCrossLinks\Model\Crosslink as ModelCrosslink;

class Save extends Crosslink
{
    /**
     * Run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('crosslink');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->prepareData($data);
            $data = $this->filterData($data);

            $id = !empty($data['crosslink_id']) ? $data['crosslink_id'] : null;

            $crosslink = $this->initCrosslink($id);
            $crosslink->setData($data);
            $this->_eventManager->dispatch(
                'mageworx_seocrosslinks_crosslink_prepare_save',
                [
                    'crosslink' => $crosslink,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $keywords = $this->_getKeywords($data);

                for ($i = 0; $i < count($keywords); $i++) {
                    if ($i == 0) {
                        $id = $crosslink->getCrosslinkId();
                        $crosslink->addData($data);
                        $crosslink->setId($id);
                    } else {
                        $multisaveFlag    = true;
                        $isReducePriority = true;
                        if ($isReducePriority && $data['priority'] > 0) {
                            $data['priority'] -= 1;
                        }
                        $crosslink->setData($data);
                        $crosslink->setCrosslinkId(null);
                    }
                    $crosslink->setKeyword($keywords[$i]);
                    $crosslink->save();
                }

                if (empty($multisaveFlag)) {
                    $successMessage = __('The crosslink has been saved.');
                } else {
                    $successMessage = __('The crosslinks has been saved with reduce priority.');
                }

                $this->messageManager->addSuccess($successMessage);
                $this->_getSession()->setMageWorxSeoCrossLinksCrosslinkData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mageworx_seocrosslinks/*/edit',
                        [
                            'crosslink_id' => $crosslink->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('mageworx_seocrosslinks/*/');
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the crosslink.'));
            }

            $this->_getSession()->setMageWorxSeoCrossLinksCrosslinkData($data);
            $resultRedirect->setPath(
                'mageworx_seocrosslinks/*/edit',
                [
                    'crosslink_id' => $crosslink->getId(),
                    '_current' => true
                ]
            );

            return $resultRedirect;
        }
        $resultRedirect->setPath('mageworx_seocrosslinks/*/');
        return $resultRedirect;
    }

    /**
     * Retrive list of keywords
     *
     * @param array $data
     * @return array
     */
    protected function _getKeywords($data)
    {
        $keywordsString = $data['keyword'];
        $keywordsArray = array_filter(preg_split('/\r?\n/', $keywordsString));
        $keywordsArray = array_map('trim', $keywordsArray);
        $keywordsArray = array_filter($keywordsArray);
        $keywordsArray = array_unique($keywordsArray);
        return (count($keywordsArray) > 1) ? array_values($keywordsArray) : array($data['keyword']);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        if ($data['reference'] != ModelCrosslink::REFERENCE_TO_STATIC_URL) {
            if (!empty($data['reference'])) {
                $data['ref_category_id'] = str_replace(['category/', 'product/'], '', $data['ref_category_id']);
                $productId = (str_replace(['category/', 'product/'], '', $data['ref_product_sku']));
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
                $data['ref_product_sku'] = $currentproduct->getSku();
            }
        }
        return $data;
    }
}
