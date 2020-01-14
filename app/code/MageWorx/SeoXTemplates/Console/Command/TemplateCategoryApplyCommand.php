<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Console\Command;

use MageWorx\SeoXTemplates\Console\Command\AbstractTemplateTypeManageCommand;

class TemplateCategoryApplyCommand extends AbstractTemplateTypeManageCommand
{
    const ENTITY_TYPE = 'category';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('xtemplate:category:apply');
        $this->setDescription('Apply category templates by ids');
        parent::configure();
    }

    /**
     *
     * @return boolean
     */
    protected function isEnable()
    {
        return true;
    }

    /**
     * Retrieve entity template type , such as product, category, etc.
     */
    protected function getEntityType()
    {
        return self::ENTITY_TYPE;
    }

    /**
     * Dispatch event
     *
     * @param array $templateIds
     * @return void
     */
    protected function performAction(array $templateIds)
    {
        $this->eventManager->dispatch(
            'mageworx_seoxtemplates_category_template_apply',
            [
                'templateIds' => $templateIds
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getDisplayMessage()
    {
        return 'Applied category template ids:';
    }

    /**
     * Retrieve finish notice
     *
     * @return string
     */
    protected function getSuccessMessage()
    {
        return 'Successful. Please, run "indexer:reindex" for refresh data.';
    }
}
