<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Block\Head\Json\Breadcrumbs;

class Catalog extends \MageWorx\SeoMarkup\Block\Head\Json\Breadcrumbs
{
    /**
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $helperCatalogData;

    /**
     *
     * @param \MageWorx\SeoMarkup\Helper\Breadcrumbs $helperBreadcrumbs
     * @param \Magento\Catalog\Helper\Data $helperCatalogData
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \MageWorx\SeoMarkup\Helper\Breadcrumbs $helperBreadcrumbs,
        \Magento\Catalog\Helper\Data $helperCatalogData,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->helperCatalogData = $helperCatalogData;
        parent::__construct($helperBreadcrumbs, $context, $data);
    }

    /**
     *
     * {@inheritDoc}
     */
    protected function getBreadcrumbs()
    {
        $crumbs = $this->getHomeBreadcrumbs();
        $path   = $this->helperCatalogData->getBreadcrumbPath();

        if (is_array($path)) {
            foreach ($path as $name => $breadcrumb) {
                $crumbs = $this->addCrumb($name, $breadcrumb, $crumbs);
            }
        }

        return $crumbs;
    }
}
