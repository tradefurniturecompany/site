<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model\Source;

use MageWorx\SeoRedirects\Model\Source;
use MageWorx\SeoRedirects\Model\Redirect\DpRedirect;

/**
 * Class Problems
 *
 * We build options on the fly using report's config
 *
 * @see \MageWorx\SeoReports\Ui\Component\Listing\Column\Problems
 */
class DynamicProblems extends \MageWorx\SeoAll\Model\Source
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [];
    }
}
