<?php

namespace WeltPixel\OwlCarouselSlider\Controller\Adminhtml\Banner;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Save Banner action.
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Save extends \WeltPixel\OwlCarouselSlider\Controller\Adminhtml\Banner
{
    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $bannerModel = $this->_bannerFactory->create();

            $bannerId = $this->getRequest()->getParam(static::PARAM_ID);

            if ($bannerId) {
                $bannerModel->load($bannerId);
            }

            $bannerImage = $this->getRequest()->getFiles('image');

            $fileName = ($bannerImage && array_key_exists('name', $bannerImage)) ? $bannerImage['name'] : null;

            if ($bannerImage && $fileName) {
                try {
                    /** @var \Magento\Framework\ObjectManagerInterface $uploader */
                    $uploader = $this->_objectManager->create(
                        'Magento\MediaStorage\Model\File\Uploader',
                        ['fileId' => 'image']
                    );

                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

                    /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapterFactory */
                    $imageAdapterFactory = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')
                        ->create();

                    $uploader->addValidateCallback('banner_image', $imageAdapterFactory, 'validateUploadFile');
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);

                    /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                    $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                        ->getDirectoryRead(DirectoryList::MEDIA);

                    $result = $uploader->save(
                        $mediaDirectory
                            ->getAbsolutePath(\WeltPixel\OwlCarouselSlider\Model\Banner::OWLCAROUSELSLIDER_MEDIA_PATH)
                    );

                    $data['image'] = \WeltPixel\OwlCarouselSlider\Model\Banner::OWLCAROUSELSLIDER_MEDIA_PATH
                        . $result['file'];

                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            } else {
                if (isset($data['image']) && isset($data['image']['value'])) {
                    if (isset($data['image']['delete'])) {
                        $data['image'] = null;
                        $data['delete_image'] = true;
                    } elseif (isset($data['image']['value'])) {
                        $data['image'] = $data['image']['value'];
                    }
                }
            }

            /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate */
            $localeDate = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\TimezoneInterface');

            $data['valid_from'] = $localeDate->date($data['valid_from'])
                ->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i');
            $data['valid_to'] = $localeDate->date($data['valid_to'])
                ->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i');

            $bannerModel->setData($data);

            try {
                $bannerModel->save();

                $this->messageManager->addSuccess(__('The banner has been saved.'));
                $this->_getSession()->setFormData(false);

                return $this->_getResultRedirect($resultRedirect, $bannerModel->getId());

            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the banner.'));
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [static::PARAM_ID => $bannerId]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
