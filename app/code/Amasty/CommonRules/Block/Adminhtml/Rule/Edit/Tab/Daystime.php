<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Block\Adminhtml\Rule\Edit\Tab;

class Daystime extends AbstractTab
{
    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->getModel();
        $form = $this->formInit($model);
        $form->setValues($model->getData());
        $form->addValues(
            [
                'id' => $model->getId()
            ]
        );
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected  function getLabel()
    {
        return __('Days and Time');
    }

    /**
     * @inheritdoc
     */
    protected function formInit($model)
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fldInfo = $form->addFieldset('daystime', ['legend' => __('Days and Time')]);

        $fldInfo->addField(
            'days',
            'multiselect',
            [
                'label' => __('Days of the Week'),
                'name' => 'days[]',
                'values' => $this->poolOptionProvider->getOptionsByProviderCode('days'),
                'note' => __('Leave empty or select all to apply the rule every day'),
            ]
        );

        $fldInfo->addField(
            'time_from',
            'select',
            [
                'label' => __('Time From:'),
                'name' => 'time_from',
                'values' => $this->poolOptionProvider->getOptionsByProviderCode('times'),
            ]
        );

        $fldInfo->addField(
            'time_to',
            'select',
            [
                'label' => __('Time To:'),
                'name' => 'time_to',
                'values' => $this->poolOptionProvider->getOptionsByProviderCode('times'),
            ]
        );

        return $form;
    }
}
