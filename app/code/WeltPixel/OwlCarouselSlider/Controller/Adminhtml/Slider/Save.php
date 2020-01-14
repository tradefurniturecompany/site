<?php

namespace WeltPixel\OwlCarouselSlider\Controller\Adminhtml\Slider;

use WeltPixel\OwlCarouselSlider\Model\Slider;

/**
 * Save Slider action
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Save extends \WeltPixel\OwlCarouselSlider\Controller\Adminhtml\Slider
{
    /**
     * Dispatch request
     *
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $formPostValues = $this->getRequest()->getPostValue();

        if (isset($formPostValues['slider'])) {
            $sliderData = $formPostValues['slider'];
            $sliderId = isset($sliderData['id']) ? $sliderData['id'] : null;

            $sliderModel = $this->_sliderFactory->create();

            $sliderModel->load($sliderId);

            $sliderModel->setData($sliderData);

            try {
                $sliderModel->save();

                if (isset($formPostValues['slider_banner'])) {
                    $bannerGridSerializedInputData = $this->_jsHelper->decodeGridSerializedInput($formPostValues['slider_banner']);
                    $bannerIds = [];

                    $bannerOrders = [];
                    foreach ($bannerGridSerializedInputData as $key => $value) {
                        $bannerIds[] = $key;
                        $bannerOrders[] = $value['sort_order'];
                    }

                    $unSelecteds = $this->_bannerCollectionFactory
                        ->create()
                        ->addFieldToFilter('slider_id', $sliderModel->getId());
                    ;

                    if (count($bannerIds)) {
                        $unSelecteds->addFieldToFilter('id', ['nin' => $bannerIds]);
                    }

                    foreach ($unSelecteds as $banner) {
                        $banner->setSliderId(0)
                            ->setSortOrder(0)->save();
                    }

                    $selectBanner = $this->_bannerCollectionFactory
                        ->create()
                        ->addFieldToFilter('id', ['in' => $bannerIds]);

                    $i = -1;
                    foreach ($selectBanner as $banner) {
                        $banner->setSliderId($sliderModel->getId())
                            ->setSortOrder($bannerOrders[++$i])->save();
                    }
                }

                $this->messageManager->addSuccess(__('The slider has been saved.'));
                $this->_getSession()->setFormData(false);

                return $this->_getResultRedirect($resultRedirect, $sliderModel->getId());
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the slider.'));
            }

            $this->_getSession()->setFormData($formPostValues);

            return $resultRedirect->setPath('*/*/edit', [static::PARAM_CRUD_ID => $sliderId]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
