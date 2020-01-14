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
 * Tab with Rate Import Form
 */
class Import extends Generic implements TabInterface
{
    public function getTabLabel()
    {
        return __('Import');
    }

    public function getTabTitle()
    {
        return __('Import');
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
        $fieldset = $form->addFieldset('import_fieldset', ['legend' => __('Import Rates')]);

        $fieldset->addField(
            'import_clear',
            'select',
            [
                'name' => 'import_clear',
                'label' => __('Delete Existing Rates'),
                'values' => [
                    [
                        'value' => 0,
                        'label' => __('No')
                    ],
                    [
                        'value' => 1,
                        'label' => __('Yes')
                    ]
                ]
            ]
        );

        $fieldset->addField(
            'import_file',
            'file',
            [
                'name' => 'import_file',
                'label' => __('CSV File'),
                'title' => __('CSV File'),
                'note' => __('Example file http://amasty.com/examples/shipping-table-rates.csv')
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
