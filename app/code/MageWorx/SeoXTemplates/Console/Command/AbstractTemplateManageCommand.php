<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;
use MageWorx\SeoXTemplates\Model\Template\ManagerFactory;

abstract class AbstractTemplateManageCommand extends Command
{
    /**
     * Input argument types
     */
    const INPUT_KEY_IDS = 'ids';

    /**
     *
     * @var ManagerFactory
     */
    protected $templateManagerFactory;

    /**
     *
     * @var \MageWorx\SeoXTemplates\Model\Template\ManagerInterface
     */
    protected $templateManager;

    /**
     * Constructor
     *
     * @param ManagerFactory $templateManagerFactory
     */
    public function __construct(ManagerFactory $templateManagerFactory)
    {
        $this->templateManagerFactory = $templateManagerFactory;
        $this->templateManager = $this->templateManagerFactory->create($this->getEntityType());
        parent::__construct();
    }

    /**
     * @return string
     */
    abstract protected function getEntityType();

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument(
            self::INPUT_KEY_IDS,
            InputArgument::IS_ARRAY,
            'Space-separated list of template ids or omit to apply to all template ids.'
        );
        parent::configure();
    }

    /**
     * Get requested cache types
     *
     * @param InputInterface $input
     * @return array
     */
    protected function getRequestedIds(InputInterface $input)
    {
        $requestedIds = [];
        if ($input->getArgument(self::INPUT_KEY_IDS)) {
            $requestedIds = $input->getArgument(self::INPUT_KEY_IDS);
            $requestedIds = array_filter(array_map('trim', $requestedIds), 'strlen');
        }

        if (empty($requestedIds)) {
            return $this->templateManager->getAvailableIds();
        } else {
            $availableIds = $this->templateManager->getAvailableIds();
            $unsupportedIds = array_diff($requestedIds, $availableIds);
            if ($unsupportedIds) {
                throw new \InvalidArgumentException(
                    "The following requested template ids are not supported: '" . join("', '", $unsupportedIds)
                    . "'." . PHP_EOL . 'Supported ids: ' . join(", ", $availableIds)
                );
            }
            return array_values(array_intersect($availableIds, $requestedIds));
        }
    }

    /**
     * @return array
     */
    protected function getColumns()
    {
        return $this->templateManager->getColumnsValues();
    }
}
