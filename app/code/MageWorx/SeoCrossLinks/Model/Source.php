<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Model;

use MageWorx\SeoAll\Helper\LandingPage;
use MageWorx\SeoAll\Model\Source as SourceModel;

abstract class Source extends SourceModel
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
