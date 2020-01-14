<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

class RedirectWizardByDeletedCategoryUrl extends RedirectWizardByDeletedUrlAbstract
{
    /**
     * @var int
     */
    protected $redirectEntityType = CustomRedirect::REDIRECT_TYPE_CATEGORY;
}
