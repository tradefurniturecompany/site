<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Block\Head\Json\Breadcrumbs;

class Home extends \MageWorx\SeoMarkup\Block\Head\Json\Breadcrumbs
{
    /**
     *
     * {@inheritDoc}
     */
    protected function getBreadcrumbs()
    {
        $crumbs = $this->getHomeBreadcrumbs();
        return $crumbs;
    }
}
