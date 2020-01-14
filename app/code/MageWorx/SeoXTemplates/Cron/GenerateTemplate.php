<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Cron;

use Magento\Framework\Event\ManagerInterface as EventManagerInterface;

abstract class GenerateTemplate
{
    /** @var EventManagerInterface */
    protected $eventManager;

    /**
     * @param EventManagerInterface $eventManager
     */
    public function __construct(
        EventManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
    }

    /**
     * Generate event by template type
     *
     * @param int $typeId
     */
    protected function generateEntityByTypeId($typeId)
    {
        $this->eventManager->dispatch(
            'mageworx_seoxtemplates_product_template_apply',
            [
                'templateTypeId' => $typeId
            ]
        );
    }

    /**
     * Generate event by template type
     *
     * @param int $typeId
     */
    protected function generateEntityByTypeIdForCategory($typeId)
    {
        $this->eventManager->dispatch(
            'mageworx_seoxtemplates_category_template_apply',
            [
                'templateTypeId' => $typeId
            ]
        );
    }

    /**
     * Generate event by template type
     *
     * @param int $typeId
     */
    protected function generateEntityByTypeIdForLandingPage($typeId)
    {
        $this->eventManager->dispatch(
            'mageworx_seoxtemplates_landingpage_template_apply',
            [
                'templateTypeId' => $typeId
            ]
        );
    }
}
