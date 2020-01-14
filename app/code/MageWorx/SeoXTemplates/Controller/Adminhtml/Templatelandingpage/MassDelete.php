<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatelandingpage;

use MageWorx\SeoXTemplates\Model\Template\LandingPage as TemplatelandingpageModel;

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
     * @param TemplatelandingpageModel $template
     * @return $this
     */
    protected function doTheAction(TemplatelandingpageModel $template)
    {
        $template->delete();
        return $this;
    }
}
