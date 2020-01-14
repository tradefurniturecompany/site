<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Block\Head\SocialMarkup\Page;

class Home extends \MageWorx\SeoMarkup\Block\Head\SocialMarkup\Page
{
    /**
     *
     * {@inheritDoc}
     */
    protected function isOgEnabled()
    {
        return $this->helperWebsite->isOgEnabled();
    }

    /**
     *
     * {@inheritDoc}
     */
    protected function isTwEnabled()
    {
        return $this->helperWebsite->isTwEnabled() && $this->helperWebsite->getTwUsername();
    }

    /**
     *
     * {@inheritDoc}
     */
    protected function getTwImageUrl()
    {
        return '';
    }

    /**
     *
     * {@inheritDoc}
     */
    protected function getTwUsername()
    {
        return $this->helperWebsite->getTwUsername();
    }

    /**
     *
     * {@inheritDoc}
     */
    protected function getOgType()
    {
        return 'website';
    }

    /**
     *
     * {@inheritDoc}
     */
    protected function getTwType()
    {
        return 'summary_large_image';
    }
}
