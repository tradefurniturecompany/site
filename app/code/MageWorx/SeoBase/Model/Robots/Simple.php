<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Robots;

/**
 * SEO Base non-specific pages robots model
 */
class Simple extends \MageWorx\SeoBase\Model\Robots
{
    /**
     * Retrieve final robots
     *
     * @return string
     */
    public function getRobots()
    {
        return $this->getRobotsBySettings();
    }
}
