<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model;

/**
 * @api
 */
interface ConverterInterface
{
    /**
     * Retrieve converted string by template code
     *
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $item
     * @param string $templateCode
     * @return string
     */
    public function convert($item, $templateCode);
}
