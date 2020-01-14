<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Observer;

class PagePrepareSaveObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Set default values for own fields in CMS Page
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $object = $product = $observer->getEvent()->getDataObject();

        foreach (['mageworx_hreflang_identifier', 'meta_robots'] as $field) {
            $value = ($object->getData($field)) === null ? '' : $object->getData($field);
            $object->setData($field, $value);
        }
    }
}
