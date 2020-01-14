<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Observer\PrepareForm\Catalog;

class MetaRobots implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \MageWorx\SeoBase\Model\Source\MetaRobots
     */
    protected $options;

    /**
     * @param \MageWorx\SeoBase\Model\Source\MetaRobots $metaRobotsOptions
     */
    public function __construct(
        \MageWorx\SeoBase\Model\Source\MetaRobots $metaRobotsOptions
    ) {
        $this->options = $metaRobotsOptions;
    }

    /**
     * Add values for "meta_robots" field
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //adminhtml_catalog_product_edit_prepare_form
        //adminhtml_catalog_category_edit_prepare_form
        $form = $observer->getForm();
        $metaRobots = $form->getElement('meta_robots');

        if ($metaRobots) {
            $metaRobots->setValues($this->options->toOptionArray());
        }
        
        return $this;
    }
}
