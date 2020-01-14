<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Controller\Adminhtml\Methods;

use Amasty\Base\Model\Serializer;
use Amasty\ShippingTableRates\Model\LabelFactory;
use Amasty\ShippingTableRates\Model\ResourceModel\Label;
use Amasty\ShippingTableRates\Model\ResourceModel\Label\CollectionFactory as LabelCollectionFactory;
use Magento\Backend\App\Action\Context;

/**
 * Save Shipping Method Action
 */
class Save extends \Amasty\ShippingTableRates\Controller\Adminhtml\Methods
{
    /**
     * @var LabelFactory
     */
    private $labelFactory;
    /**
     * @var Serializer
     */
    private $serializerBase;
    /**
     * @var LabelCollectionFactory
     */
    private $collectionFactory;
    /**
     * @var Label
     */
    private $resourceLabel;

    /**
     * @var \Amasty\ShippingTableRates\Model\MethodFactory
     */
    private $methodFactory;

    /**
     * @var \Amasty\ShippingTableRates\Model\ResourceModel\Rate
     */
    private $rateResource;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var \Amasty\ShippingTableRates\Model\Rate\Import\RateImportService
     */
    private $rateImport;

    public function __construct(
        Context $context,
        LabelFactory $labelFactory,
        LabelCollectionFactory $collectionFactory,
        Label $resourceLabel,
        Serializer $serializerBase,
        \Amasty\ShippingTableRates\Model\MethodFactory $methodFactory,
        \Amasty\ShippingTableRates\Model\ResourceModel\Rate $rateResource,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Amasty\ShippingTableRates\Model\Rate\Import\RateImportService $rateImport
    ) {
        parent::__construct($context);
        $this->labelFactory = $labelFactory;
        $this->serializerBase = $serializerBase;
        $this->collectionFactory = $collectionFactory;
        $this->resourceLabel = $resourceLabel;
        $this->methodFactory = $methodFactory;
        $this->rateResource = $rateResource;
        $this->uploaderFactory = $uploaderFactory;
        $this->rateImport = $rateImport;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /**
         * @var \Amasty\ShippingTableRates\Model\Method $modelMethod
         */
        $modelMethod = $this->methodFactory->create();
        $data = $this->getPostData();
        if ($data) {
            $modelMethod->setData($data);
            $modelMethod->setId($id);

            try {
                $this->prepareCommentImgForSave($modelMethod);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $id]);
                return;
            }

            if (($data['min_rate'] > $data['max_rate']) && ($data['max_rate'] > 0) && ($data['min_rate'] > 0)) {
                $this->messageManager->addErrorMessage(
                    'Minimal rate must be less than maximal rate, please check your restrictions'
                );
                $this->_redirect('*/*/edit', ['id' => $modelMethod->getId()]);

                return;
            }

            try {
                $noFile = false;
                $this->prepareForSave($modelMethod);
                $modelMethod->save();
                $this->prepareForSaveLabels($data, $modelMethod->getId());
                if ($modelMethod->getData('import_clear')) {
                    $this->rateResource->deleteBy($modelMethod->getId());
                }

                try {
                    /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
                    $uploader = $this->uploaderFactory->create(['fileId' => 'import_file']);
                } catch (\Exception $e) {
                    $noFile = true;
                }

                // import files
                if (!$noFile) {
                    $uploader->setAllowedExtensions('csv');
                    $fileData = $uploader->validateFile();

                    $fileName = $fileData['tmp_name'];
                    //phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
                    ini_set('auto_detect_line_endings', 1);

                    $errors = $this->rateImport->import($modelMethod->getId(), $fileName);
                    foreach ($errors as $err) {
                        $this->messageManager->addErrorMessage($err);
                    }
                }

                $msg = __('Shipping rates have been successfully saved');
                $this->messageManager->addSuccessMessage($msg);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $modelMethod->getId()]);
                } else {
                    $this->_redirect('*/*');
                }
            } catch (\Exception $e) {
                $errMessage = $e->getMessage();
                if ($errMessage == 'Disallowed file type.') {
                    $errMessage = $errMessage . ' Please use CSV format of file for import';
                }
                $this->messageManager->addErrorMessage($errMessage);
                $this->_redirect('*/*/edit', ['id' => $id]);
            }

            return;
        }

        $this->messageManager->addErrorMessage(__('Unable to find a record to save'));
        $this->_redirect('*/*');
    }

    public function prepareForSave($model)
    {
        $fields = ['stores', 'cust_groups', 'free_types'];
        foreach ($fields as $f) {
            // convert data from array to string
            $val = $model->getData($f);
            $model->setData($f, '');
            if (is_array($val)) {
                // need commas to simplify sql query
                $model->setData($f, ',' . implode(',', $val) . ',');
            }
        }

        return true;
    }

    /**
     * @param $data
     * @param $methodId
     */
    private function prepareForSaveLabels($data, $methodId)
    {
        $storesData = [];
        if (isset($data['label_']) && is_array($data['label_'])) {
            foreach ($data['label_'] as $store => $storeLabel) {
                $storesData[$store]['label'] = $storeLabel;
            }
        }

        if (isset($data['comment_']) && is_array($data['comment_'])) {
            foreach ($data['comment_'] as $store => $storeComment) {
                $storesData[$store]['comment'] = $storeComment;
            }
        }
        foreach ($storesData as $storeId => $storeData) {
            /** @var \Amasty\ShippingTableRates\Model\Label $modelLabel */
            $modelLabel = $this->labelFactory->create();
            if (isset($data['id'])) {
                $modelLabel = $this->collectionFactory->create()
                    ->addFieldToFilter('method_id', $data['id'])
                    ->addFieldToFilter('store_id', $storeId)
                    ->getFirstItem();
            }
            $modelLabel->setStoreId($storeId);
            $modelLabel->setLabel($storeData['label']);
            $modelLabel->setComment($storeData['comment']);
            $modelLabel->setMethodId($methodId);
            $this->resourceLabel->save($modelLabel);
        }
    }

    /**
     * @param $modelMethod
     * @return $this
     */
    private function prepareCommentImgForSave($modelMethod)
    {
        $files = $this->getRequest()->getFiles();
        $image = $modelMethod->getCommentImg();

        if ($files && isset($files['comment_img']) && strlen($files['comment_img']['name'])) {
            $img = $modelMethod->saveImage($files['comment_img']);
            $modelMethod->setCommentImg($img);
        } elseif ($image && isset($image['delete']) && $image['delete']) {
            $modelMethod->setCommentImg('');
        } elseif ($image && isset($image['value']) && $image['value']) {
            $modelMethod->setCommentImg($image['value']);
        }

        return $this;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_ShippingTableRates::amstrates');
    }

    /**
     * @return array
     */
    protected function getPostData()
    {
        return $this->getRequest()->getPostValue();
    }
}
