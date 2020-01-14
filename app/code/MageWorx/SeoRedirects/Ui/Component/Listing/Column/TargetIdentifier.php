<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Ui\Component\Listing\Column;

use MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

class TargetIdentifier extends RequestIdentifier
{
    /**
     * @var string
     */
    protected $entityType = CustomRedirectInterface::TARGET_ENTITY_TYPE;

    /**
     * @var string
     */
    protected $entityIdentifier = CustomRedirectInterface::TARGET_ENTITY_IDENTIFIER;
}
