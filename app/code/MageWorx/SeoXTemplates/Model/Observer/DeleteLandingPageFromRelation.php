<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Observer;

use MageWorx\SeoXTemplates\Model\ResourceModel\Template\LandingPage\CollectionFactory;

/**
 * Observer class for cleaning of landing page relation using the deleted landing page
 */
class DeleteLandingPageFromRelation implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $resourceFactory;

    /**
     * DeleteLandingPageFromRelation constructor.
     *
     * @param CollectionFactory $resourceFactory
     */
    public function __construct(
        CollectionFactory $resourceFactory
    ) {
        $this->resourceFactory  = $resourceFactory;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $id = $observer->getData('id');
        if (!is_array($id)) {
            $id = [$id];
        }

        /** @var MageWorx\SeoXTemplates\Model\ResourceModel\Template\LandingPage\Collection $resource */
        $resource = $this->resourceFactory->create();
        $resource->getConnection()->delete(
            $resource->getTable('mageworx_seoxtemplates_template_relation_landingpage'),
            ['landingpage_id IN (?)' => $id]
        );

    }
}