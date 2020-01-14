<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Observer;

use MageWorx\SeoXTemplates\Model\DynamicRenderer\LandingPage as Renderer;

/**
 * Observer class for landing page template apply proccess
 */

class LandingPageDataModifier implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\SeoXTemplates\Model\DynamicRenderer\LandingPage
     */
    protected $dynamicRenderer;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * LandingPageDataModifier constructor.
     * @param Renderer $dynamicRenderer
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Renderer $dynamicRenderer,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry
    ) {
        $this->dynamicRenderer = $dynamicRenderer;
        $this->request = $request;
        $this->registry = $registry;
    }

    /**
     * Modify LandingPage data and meta head
     * Event: layout_generate_blocks_after
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ('mageworx_landingpagespro_landingpage_view' == $this->request->getFullActionName()) {

            $landingPage = $this->registry->registry('mageworx_landingpagespro_landingpage');
            if (is_object($landingPage)) {
                $this->dynamicRenderer->modifyLandingPageTitle($landingPage);
                $this->dynamicRenderer->modifyLandingPageMetaDescription($landingPage);
                $this->dynamicRenderer->modifyLandingPageMetaKeywords($landingPage);
                $this->dynamicRenderer->modifyLandingPageTexts($landingPage);
            }
        }
    }
}
