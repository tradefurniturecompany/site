<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Block\Adminhtml\Methods\Edit\Tab;

use Amasty\ShippingTableRates\Model\Rate;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * General Tab
 */
class Main extends Generic implements TabInterface
{
    /**
     * @var \Amasty\ShippingTableRates\Model\Config\Source\Statuses
     */
    private $statuses;

    /**
     * @var \Amasty\ShippingTableRates\Helper\Data
     */
    private $helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Amasty\ShippingTableRates\Model\Config\Source\Statuses $statuses,
        \Amasty\ShippingTableRates\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->statuses = $statuses;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getTabLabel()
    {
        return __('General');
    }

    public function getTabTitle()
    {
        return __('General');
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
        $fieldsetGeneral = $form->addFieldset('general_fieldset', ['legend' => __('General')]);
        if ($model->getId()) {
            $fieldsetGeneral->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldsetGeneral->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'note' => __(
                    'Specify the "estimated delivery" value for the {day} variable on the Rate Configuration page'
                    . ' or it will be automatically taken from the uploaded CSV file. '
                    . 'Specify the "delivery name" value for the {name} variable on the Rate Configuration page'
                    . ' or it will be automatically taken from the uploaded CSV file.'
                )
            ]
        );

        $fieldsetGeneral->addField(
            'free_types',
            'multiselect',
            [
                'name' => 'free_types',
                'label' => __('Ship These Shipping Types for Free'),
                'title' => __('Ship These Shipping Types for Free'),
                'values' => $this->helper->getTypes(),
                'note' => __(
                    'Products will be sent for free if the method does not contain rates applicable for them. '
                    . 'If there will be no applicable rates in the method it won\'t be displayed as products with'
                    . ' Shipping Types above will be ignored in calculations.'
                )
            ]
        );

        $fieldsetGeneral->addField(
            'comment',
            'textarea',
            [
                'name' => 'comment',
                'label' => __('Comment'),
                'title' => __('Comment'),
                'note' => $this->escapeHtml(__('HTML tags <b>, <u>, <i>, <s> are supported.
                    For example: This is a <b>Bold text</b>. To learn more, refer to this page: '))
                    . '<a href="' . $this->escapeUrl('https://www.w3schools.com/html/html_css.asp')
                    . '" title="' . __('HTML Styles - CSS')
                    . '" target="_blank">' . $this->escapeUrl('https://www.w3schools.com/html/html_css.asp') . '</a>'
            ]
        );

         $fieldsetGeneral->addField(
             'comment_img',
             'image',
             [
                'name' => 'comment_img',
                'label' => __('Image'),
                'title' => __('Image'),
                'note' => __('Please use {IMG} in Comment field to insert an image')
             ]
         );

        $fieldsetGeneral->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'label' => __('Status'),
                'title' => __('Status'),
                'options' => $this->statuses->toOptionArray(),
            ]
        );

        $fieldsetRates = $form->addFieldset('rates_fieldset', ['legend' => __('Rates')]);

        $fieldsetRates->addField(
            'min_rate',
            'text',
            [
                'name' => 'min_rate',
                'label' => __('Minimal rate'),
                'title' => __('Minimal rate'),
            ]
        );

        $fieldsetRates->addField(
            'max_rate',
            'text',
            [
                'name' => 'max_rate',
                'label' => __('Maximal rate'),
                'title' => __('Maximal rate'),
            ]
        );

        $fieldsetRates->addField(
            'select_rate',
            'select',
            [
                'name' => 'select_rate',
                'label' => __('For products with different shipping types'),
                'title' => __('For products with different shipping types'),
                'values' => [
                    [
                        'value' => Rate::ALGORITHM_SUM,
                        'label' => __('Sum up rates')
                    ],
                    [
                        'value' => Rate::ALGORITHM_MAX,
                        'label' => __('Select maximal rate')
                    ],
                    [
                        'value' => Rate::ALGORITHM_MIN,
                        'label' => __('Select minimal rate')
                    ]
                ]
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
