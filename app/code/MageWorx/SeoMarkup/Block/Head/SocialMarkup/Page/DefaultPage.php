<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Block\Head\SocialMarkup\Page;

class DefaultPage extends \MageWorx\SeoMarkup\Block\Head\SocialMarkup\Page
{
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
    protected function isOgEnabled()
    {
        return $this->helperPage->isOgEnabled();
    }

    /**
     *
     * {@inheritDoc}
     */
    protected function isTwEnabled()
    {
        return $this->helperPage->isTwEnabled() && $this->helperPage->getTwUsername();
    }

    /**
     *
     * {@inheritDoc}
     */
    protected function getTwUsername()
    {
        return $this->helperPage->getTwUsername();
    }

    /**
     *
     * {@inheritDoc}
     */
    protected function getOgType()
    {
        return 'article';
    }

    /**
     *
     * {@inheritDoc}
     */
    protected function getTwType()
    {
        return 'summary';
    }
}
