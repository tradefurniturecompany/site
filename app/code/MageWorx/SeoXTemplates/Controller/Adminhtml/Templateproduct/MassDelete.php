<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templateproduct;

use MageWorx\SeoXTemplates\Model\Template\Product as TemplateProductModel;

class MassDelete extends MassAction
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 record(s) have been deleted';

    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while deleting record(s).';

    /**
     *
     * @param TemplateProductModel $template
     * @return $this
     */
    protected function doTheAction(TemplateProductModel $template)
    {
        $template->delete();
        return $this;
    }
}
