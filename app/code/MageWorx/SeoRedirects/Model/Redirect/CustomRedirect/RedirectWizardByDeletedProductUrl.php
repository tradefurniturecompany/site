<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

class RedirectWizardByDeletedProductUrl extends RedirectWizardByDeletedUrlAbstract
{
    /**
     * @var int
     */
    protected $redirectEntityType = CustomRedirect::REDIRECT_TYPE_PRODUCT;

    /**
     * @param $rewrite
     * @return bool
     */
    protected function getIsModifyRedirectByTargetEntity($rewrite)
    {
        $result = parent::getIsModifyRedirectByTargetEntity($rewrite);

        if ($result && strpos($rewrite->getTargetPath(), 'category') === false) {
            return true;
        }

        return false;
    }
}
