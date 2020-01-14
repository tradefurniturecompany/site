<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Block\Adminhtml\Rates\Edit;

/**
 * Shipping Rate of Method Form initialization
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Amasty\ShippingTableRates\Helper\Data $helper,
        array $data
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('amstrates_rate_form');
        $this->setTitle(__('Rate Information'));
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('amtable_rate');

        /**
         * @var \Magento\Framework\Data\Form $form
         */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('amstrates/rates/save'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );

        $fieldsetDestination = $form->addFieldset('destination', ['legend' => __('Destination')]);

        if ($model->getId()) {
            $fieldsetDestination->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldsetDestination->addField(
            'method_id',
            'hidden',
            [
                'name' => 'method_id'
            ]
        );

        $fieldsetDestination->addField(
            'country',
            'select',
            [
                'name' => 'country',
                'label' => __('Country'),
                'options' => $this->_helper->getCountries(true),

            ]
        );

        $fieldsetDestination->addField(
            'state',
            'select',
            [
                'name' => 'state',
                'label' => __('State'),
                'options' => $this->_helper->getStates(true),

            ]
        );

        $fieldsetDestination->addField(
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
            ]
        );

        $fieldsetDestination->addField(
            'zip_from',
            'text',
            [
                'label' => __('Zip From'),
                'name' => 'zip_from'
            ]
        );

        $fieldsetDestination->addField(
            'zip_to',
            'text',
            [
                'label' => __('Zip To'),
                'name' => 'zip_to'
            ]
        );

        $fieldsetConditions = $form->addFieldset('conditions', ['legend' => __('Conditions')]);

        $fieldsetConditions->addField(
            'weight_from',
            'text',
            [
                'label' => __('Weight From'),
                'name' => 'weight_from',
            ]
        );

        $fieldsetConditions->addField(
            'weight_to',
            'text',
            [
                'label' => __('Weight To'),
                'name' => 'weight_to',
            ]
        );

        $fieldsetConditions->addField(
            'qty_from',
            'text',
            [
                'label' => __('Qty From'),
                'name' => 'qty_from',
            ]
        );

        $fieldsetConditions->addField(
            'qty_to',
            'text',
            [
                'label' => __('Qty To'),
                'name' => 'qty_to',
            ]
        );

        $fieldsetConditions->addField(
            'shipping_type',
            'select',
            [
                'label' => __('Shipping Type'),
                'name' => 'shipping_type',
                'options' => $this->_helper->getTypes(true),
            ]
        );

        $fieldsetConditions->addField(
            'price_from',
            'text',
            [
                'label' => __('Price From'),
                'name' => 'price_from',
                'note' => __('Original product cart price, without discounts.'),
            ]
        );

        $fieldsetConditions->addField(
            'price_to',
            'text',
            [
                'label' => __('Price To'),
                'name' => 'price_to',
                'note' => __('Original product cart price, without discounts.'),
            ]
        );

        $fieldsetConditions->addField(
            'time_delivery',
            'text',
            [
                'label' => __('Estimated Delivery (days)'),
                'name' => 'time_delivery',
                'note' => __('This value will be used for the {day} variable in the Method name')
            ]
        );

        $fieldsetConditions->addField(
            'name_delivery',
            'text',
            [
                'label' => __('Name delivery'),
                'name' => 'name_delivery',
                'note' => __('This value will be used for the {name} variable in the Method name')
            ]
        );

        $fieldsetRate = $form->addFieldset('rate', ['legend' => __('Rate')]);

        $fieldsetRate->addField(
            'cost_base',
            'text',
            [
                'label' => __('Base Rate for the Order'),
                'name' => 'cost_base',
            ]
        );

        $fieldsetRate->addField(
            'cost_percent',
            'text',
            [
                'label' => __('Percentage per Product'),
                'name' => 'cost_percent',
            ]
        );

        $fieldsetRate->addField(
            'cost_product',
            'text',
            [
                'label' => __('Fixed Rate per Product'),
                'name' => 'cost_product',
            ]
        );

        $fieldsetRate->addField(
            'cost_weight',
            'text',
            [
                'label' => __('Fixed Rate per 1 unit of weight'),
                'name' => 'cost_weight',
            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
