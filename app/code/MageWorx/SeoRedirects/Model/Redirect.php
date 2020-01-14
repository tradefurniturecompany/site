<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model;

use Magento\Framework\Phrase;

/**
 * Abstract model class
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
abstract class Redirect extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Redirect "Moved Permanently" code
     *
     * @var int
     */
    const CODE_MOVED_PERMANENTLY = 301;

    /**
     * Redirect "Found" code
     *
     * @var int
     */
    const CODE_FOUND = 302;

    /**
     * Status disabled
     *
     * @var int
     */
    const STATUS_DISABLED = 0;

    /**
     * Status disabled
     *
     * @var int
     */
    const STATUS_ENABLED = 1;
}
