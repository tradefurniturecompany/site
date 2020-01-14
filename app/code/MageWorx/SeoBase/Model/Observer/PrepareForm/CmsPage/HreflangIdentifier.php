<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Observer\PrepareForm\CmsPage;

use \MageWorx\SeoBase\Helper\Hreflangs as HelperHreflangs;

class HreflangIdentifier implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\SeoBase\Helper\Data
     */
    protected $helperData;

    public function __construct(\MageWorx\SeoBase\Helper\Data $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * Add "Hreflang Identifier" field
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //adminhtml_cms_page_edit_tab_meta_prepare_form
        $form   = $observer->getForm();
        $fieldset = $form->getElements()->searchById('meta_fieldset');

        $fieldset->addField(
            'mageworx_hreflang_identifier',
            'text',
            [
                'name'   => 'mageworx_hreflang_identifier',
                'label'  => __('Hreflang Identifier'),
                'title'  => __('Hreflang Identifier'),
                'class'  => 'validate-identifier',
                'note'   => $this->getNote(),
            ]
        );

        return $this;
    }

    protected function getNote()
    {
        if ($this->helperData->getCmsPageRelationWay() == HelperHreflangs::CMS_RELATION_BY_IDENTIFIER) {
            $note = __('The setting is enabled. You can see the other options in');
        } else {
            $note = __('This setting is disabled. You can enable it in');
        }
        $note .= __('<i>SEO -> SEO Hreflangs URLs</i> config section');
        $note .= '<br>' . __('This setting was added by MageWorx SEO Suite');

        return $note;
    }
}
