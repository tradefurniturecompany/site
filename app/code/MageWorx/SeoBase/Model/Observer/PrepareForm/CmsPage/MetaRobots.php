<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Observer\PrepareForm\CmsPage;

class MetaRobots implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \MageWorx\SeoBase\Model\Source\MetaRobots
     */
    protected $metaRobotsOptions;

    public function __construct(
        \MageWorx\SeoBase\Model\Source\MetaRobots $metaRobotsOptions
    ) {
        $this->metaRobotsOptions = $metaRobotsOptions;
    }

    /**
     * Add values for "meta_robots" field
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //adminhtml_cms_page_edit_tab_meta_prepare_form
        $form   = $observer->getForm();
        $fieldset = $form->getElements()->searchById('meta_fieldset');

        $fieldset->addField(
            'meta_robots',
            'select',
            [
                'name'   => 'meta_robots',
                'label'  => __('Meta Robots'),
                'title'  => __('Meta Robots'),
                'values' => $this->metaRobotsOptions->toOptionArray(),
                'note'   => __('This setting was added by MageWorx SEO Suite')

            ]
        );

        return $this;
    }
}
