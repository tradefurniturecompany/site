<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategory;

use MageWorx\SeoXTemplates\Model\Template\Category as TemplateCategoryModel;

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
     * @param TemplateCategoryModel $template
     * @return $this
     */
    protected function doTheAction(TemplateCategoryModel $template)
    {
        $template->delete();
        return $this;
    }
}
