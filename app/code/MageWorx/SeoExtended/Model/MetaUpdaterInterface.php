<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoExtended\Model;

/**
 * @api
 */
interface MetaUpdaterInterface
{
    public function update($onlyFilterReplace = false);
}
