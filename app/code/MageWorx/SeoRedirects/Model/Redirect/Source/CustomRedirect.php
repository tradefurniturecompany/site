<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\Source;

use MageWorx\SeoAll\Helper\LandingPage;

abstract class CustomRedirect extends \MageWorx\SeoAll\Model\Source
{
    /**
     * @var LandingPage
     */
    protected $landingPage;

    /**
     * DefaultDestination constructor.
     *
     * @param LandingPage $landingPage
     */
    public function __construct(LandingPage $landingPage)
    {
        $this->landingPage = $landingPage;
    }
}