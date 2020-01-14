<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Observer\PrepareForm\Attribute;

class LayeredNavigationCanonical implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\SeoBase\Model\Source\FilterCanonical
     */
    protected $options;

    /**
     *
     * @param \MageWorx\SeoBase\Model\Source\FilterCanonical $canonicalForFilterOptions
     */
    public function __construct(
        \MageWorx\SeoBase\Model\Source\FilterCanonical $canonicalForFilterOptions
    ) {
        $this->options = $canonicalForFilterOptions;
    }

    /**
     * Add "Canonical Tag for Pages Filtered by Layered Navigation Leads to" field for product attributes
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        ///adminhtml_catalog_product_attribute_edit_frontend_prepare_form
        $form     = $observer->getForm();
        $fieldset = $form->getElements()->searchById('front_fieldset');
        if (!is_null($fieldset)) {
            $fieldset->addField(
                'layered_navigation_canonical',
                'select',
                [
                    'name'   => 'layered_navigation_canonical',
                    'label'  => __('Canonical Tag for Pages Filtered by Layered Navigation Leads to'),
                    'title'  => __('Canonical Tag for Pages Filtered by Layered Navigation Leads to'),
                    'values' => $this->options->toOptionArray(),
                ],
                'is_filterable_in_search'
            );
        }
        return $this;
    }
}
