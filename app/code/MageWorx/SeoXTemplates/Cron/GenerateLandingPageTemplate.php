<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Cron;

use \MageWorx\SeoXTemplates\Model\Template\LandingPage;

class GenerateLandingPageTemplate extends GenerateTemplate
{
    /**
     * Dispatch event for landing page meta title
     */
    public function generateMetaTitle()
    {
        $this->generateEntityByTypeIdForLandingPage(
            LandingPage::TYPE_LANDING_PAGE_META_TITLE
        );
    }

    /**
     * Dispatch event for landing page meta description
     */
    public function generateMetaDescription()
    {
        $this->generateEntityByTypeIdForLandingPage(
            LandingPage::TYPE_LANDING_PAGE_META_DESCRIPTION
        );
    }

    /**
     * Dispatch event for landing page meta keywords
     */
    public function generateMetaKeywords()
    {
        $this->generateEntityByTypeIdForLandingPage(
            LandingPage::TYPE_LANDING_PAGE_META_KEYWORDS
        );
    }

    /**
     * Dispatch event for landing page texts
     */
    public function generateText()
    {
        $this->generateEntityByTypeIdForLandingPage(
           LandingPage::TYPE_LANDING_PAGE_TEXT_1
        );
        $this->generateEntityByTypeIdForLandingPage(
            LandingPage::TYPE_LANDING_PAGE_TEXT_2
        );
        $this->generateEntityByTypeIdForLandingPage(
            LandingPage::TYPE_LANDING_PAGE_TEXT_3
        );
        $this->generateEntityByTypeIdForLandingPage(
            LandingPage::TYPE_LANDING_PAGE_TEXT_4
        );
    }

    /**
     * Dispatch event for landing page header
     */
    public function generateHeader()
    {
        $this->generateEntityByTypeIdForLandingPage(
            LandingPage::TYPE_LANDING_PAGE_HEADER
        );
    }

    /**
     * Dispatch event for landing page url key
     */
    public function generateUrlKey()
    {
        $this->generateEntityByTypeIdForLandingPage(
            LandingPage::TYPE_LANDING_PAGE_URL_KEY
        );
    }
}
