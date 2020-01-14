<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Console\Command;

use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use MageWorx\SeoXTemplates\Model\Template\ManagerFactory;

abstract class AbstractTemplateTypeManageCommand extends AbstractTemplateManageCommand
{
    /** @var EventManagerInterface */
    protected $eventManager;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @param Manager $templateManagerFactory
     * @param EventManagerInterface $eventManager
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        ManagerFactory $templateManagerFactory,
        \Magento\Framework\App\State $appState,
        EventManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
        $this->appState = $appState;
        parent::__construct($templateManagerFactory);
    }

    /**
     * Perform a cache management action on cache types
     *
     * @param array $cacheTypes
     * @return void
     */
    abstract protected function performAction(array $cacheTypes);

    /**
     * Get display message
     *
     * @return string
     */
    abstract protected function getDisplayMessage();

    /**
     * Get display notice
     *
     * @return string
     */
    abstract protected function getSuccessMessage();

    /**
     * Perform cache management action
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ids = $this->getRequestedIds($input);
        $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);

        if (empty($input->getArgument("ids"))) {
            $output->writeln($this->getDisplayMessage());
            $output->writeln($this->getColumns());
        }
        else {
            $this->performAction($ids);
            $output->writeln($this->getSuccessMessage());
        }
    }
}