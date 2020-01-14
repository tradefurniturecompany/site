<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Block\Adminhtml\Template\CategoryFilter\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Categories extends GenericForm implements TabInterface
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \MageWorx\SeoXTemplates\Model\Template\CategoryFilter $template */
        $template = $this->_coreRegistry->registry('mageworx_seoxtemplates_template');

        $form     = $this->_formFactory->create();
        $form->setHtmlIdPrefix('template_');
        $form->setFieldNameSuffix('template');
        $fieldset = $form->addFieldset('base_fieldset', [
            'legend'=>__('Categories'),
            'class' => 'fieldset-wide']);
        $fieldset->addField('categories_data', '\MageWorx\SeoXTemplates\Block\Adminhtml\Helper\Category', [
            'name'      => 'categories_data',
            'label'     => __('Categories'),
            'title'     => __('Categories'),
        ]);

        if (is_null($template->getCategoriesIds())) {
            $template->setCategoriesIds($template->getCategoryIds());
        }
        $form->addValues($template->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Categories');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
