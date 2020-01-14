<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */

namespace Amasty\ShippingTableRates\Controller\Adminhtml\Rates;

use Magento\Backend\App\Action\Context;

/**
 * Rate Save Action
 */
class Save extends \Magento\Backend\App\Action
{
    protected $_coreRegistry;
    /**
     * @var \Amasty\ShippingTableRates\Helper\Data
     */
    private $helperSTR;
    /**
     * @var \Amasty\ShippingTableRates\Model\RateFactory
     */
    private $rateFactory;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        Context $context,
        \Amasty\ShippingTableRates\Helper\Data $helperSTR,
        \Amasty\ShippingTableRates\Model\RateFactory $rateFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->helperSTR = $helperSTR;
        $this->rateFactory = $rateFactory;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var \Amasty\ShippingTableRates\Model\Rate $model */
        $model = $this->rateFactory->create();

        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->messageManager->addErrorMessage(__('Unable to find a rate to save'));
            $this->_redirect('adminhtml/amtable_method/index');
            return;
        }

        $isValid = $this->_checkData($data, $id);

        if ($isValid) {
            try {
                $methodId = $model->getMethodId();
                if (!$methodId) {
                    $methodId = $data['method_id'];
                }

                $fullZipFrom = $this->helperSTR->getDataFromZip($data['zip_from']);
                $fullZipTo = $this->helperSTR->getDataFromZip($data['zip_to']);
                $data['num_zip_from'] = $fullZipFrom['district'];
                $data['num_zip_to'] = $fullZipTo['district'];
                $model->setData($data)->setId($id);
                $model->save();

                $msg = __('Rate has been successfully saved');
                $this->messageManager->addSuccessMessage($msg);

                //fix for save and continue of new rates
                if ($id === null) {
                    $id = $model->getId();
                }

                if ($this->getRequest()->getParam('to_method')) {
                    $this->_redirect('amstrates/methods/edit', ['id' => $methodId]);
                } else {
                    $this->_redirect(
                        'amstrates/rates/newAction',
                        [
                            'method_id' => $methodId,
                        ]
                    );
                }

            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('This rate already exist!'));
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $id, 'method_id' => $methodId]);
            }
        } else {
            $this->_redirect('*/*/edit', ['id' => $id]);
        }
    }

    protected function _checkData($data)
    {
        $isValid = true;

        $checkKeys = [
            ['weight_from', 'weight_to'],
            ['qty_from', 'qty_to'],
            ['price_from', 'price_to']
        ];

        $keysLabels = [
            'weight_from' => __('Weight From'),
            'weight_to' => __('Weight To'),
            'qty_from' => __('Qty From'),
            'qty_to' => __('Qty To'),
            'price_from' => __('Price From'),
            'price_to' => __('Price To'),
        ];

        foreach ($checkKeys as $keys) {
            if ($data[$keys[0]] > $data[$keys[1]]) {
                $this->messageManager->addErrorMessage($keysLabels[$keys[0]]
                    . ' ' . __('must be less than') . ' ' . $keysLabels[$keys[1]]);
                $isValid = false;
            }
        }

        return $isValid;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_ShippingTableRates::amstrates');
    }
}
