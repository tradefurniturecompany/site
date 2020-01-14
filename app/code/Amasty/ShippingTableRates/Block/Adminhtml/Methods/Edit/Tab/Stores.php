<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Block\Adminhtml\Methods\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Scope Tab
 */
class Stores extends Generic implements TabInterface
{
    protected $_systemStore;
    protected $_groupRepository;
    protected $_searchCriteriaBuilder;
    protected $_objectConverter;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\Convert\DataObject $objectConverter,
        array $data = []
    ) {
        $this->_groupRepository = $groupRepository;
        $this->_systemStore = $systemStore;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_objectConverter = $objectConverter;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getTabLabel()
    {
        return __('Stores & Customer Groups');
    }

    public function getTabTitle()
    {
        return __('Stores & Customer Groups');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_amasty_table_method');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('amstrates_');
        $fieldsetStores = $form->addFieldset('stores_fieldset', ['legend' => __('Visible In')]);

        $fieldsetStores->addField(
            'stores',
            'multiselect',
            [
                'name' => 'stores[]',
                'label' => __('Stores'),
                'title' => __('Stores'),
                'values'    => $this->_systemStore->getStoreValuesForForm(),
                'note'=>__('Leave empty if there are no restrictions')
            ]
        );

        $fieldsetCustomers = $form->addFieldset('customers_fieldset', ['legend' => __('Applicable For')]);

        $customerGroups = $this->_groupRepository->getList($this->_searchCriteriaBuilder->create())->getItems();
        $fieldsetCustomers->addField(
            'cust_groups',
            'multiselect',
            [
                'name' => 'cust_groups[]',
                'label' => __('Customer Groups'),
                'title' => __('Customer Groups'),
                'note'=>__('Leave empty if there are no restrictions'),
                'values' => $this->_objectConverter->toOptionArray($customerGroups, 'id', 'code')
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
