<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Model\Observer\PrepareForm\CmsPage;

class InSitemap implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Add values for "in_html_sitemap" field
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //adminhtml_cms_page_edit_tab_meta_prepare_form
        $form     = $observer->getForm();
        $fieldset = $form->getElements()->searchById('meta_fieldset');

        $fieldset->addField(
            'in_html_sitemap',
            'select',
            [
                'name'   => 'in_html_sitemap',
                'label'  => __('In HTML Sitemap'),
                'title'  => __('In HTML Sitemap'),
                'values' => [0 => __('No'), 1 => __('Yes')],
                'note'   => __('This setting was added by MageWorx HTML Sitemap')
            ]
        );

        return $this;
    }
}
