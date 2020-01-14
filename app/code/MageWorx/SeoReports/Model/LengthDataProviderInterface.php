<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model;

/**
 * @api
 */
interface LengthDataProviderInterface
{
    /**
     * @return int|null
     */
    public function getMinLength();

    /**
     * @return int|null
     */
    public function getMaxLength();
}
