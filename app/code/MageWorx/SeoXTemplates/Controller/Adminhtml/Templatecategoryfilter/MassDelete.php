<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategoryfilter;

use MageWorx\SeoXTemplates\Model\Template\CategoryFilter as TemplateCategoryFilterModel;

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
     * @param TemplateCategoryFilterModel $template
     * @return $this
     */
    protected function doTheAction(TemplateCategoryFilterModel $template)
    {
        $template->delete();
        return $this;
    }
}
