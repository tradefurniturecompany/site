<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Controller\Adminhtml\Methods;

/**
 * Edit Shipping Method Action
 */
class Edit extends \Amasty\ShippingTableRates\Controller\Adminhtml\Methods
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Amasty\ShippingTableRates\Model\MethodFactory
     */
    private $methodFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Amasty\ShippingTableRates\Model\MethodFactory $methodFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->methodFactory = $methodFactory;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /**
         * @var \Amasty\ShippingTableRates\Model\Method $model
         */
        $model = $this->methodFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('Record does not exist.'));
                $this->_redirect('amstrates/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->coreRegistry->register('current_amasty_table_method', $model);
        $this->_view->loadLayout();
        $this->_setActiveMenu('Amasty_ShippingTableRates::amstrates')->_addBreadcrumb(__('Table Rates'), __('Table Rates'));
        if ($model->getId()) {
            $title = __('Edit Method `%1`', $model->getName());
        } else {
            $title = __("Add new Shipping Table Rate");
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);

        $this->_view->renderLayout();
    }

    protected function prepareForEdit($model)
    {
        $fields = ['stores', 'cust_groups', 'free_types'];
        foreach ($fields as $f) {
            $val = $model->getData($f);
            if (!is_array($val)) {
                $model->setData($f, explode(',', $val));
            }
        }
        return true;
    }
}
