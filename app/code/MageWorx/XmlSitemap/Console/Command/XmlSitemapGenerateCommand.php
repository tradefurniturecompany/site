<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class XmlSitemapGenerateCommand extends AbstractSitemapManageCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('xmlsitemap:generate');
        $this->setDescription('Generate the XML sitemap with specified IDs(separate IDs by space).'
            . ' The IDs can be found in the XML sitemap grid.');
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
     * Dispatch event
     *
     * @param array $sitemapIds
     * @return void
     */
    protected function performAction(array $sitemapIds)
    {
        $this->eventManager->dispatch(
            'mageworx_xmlsitemap_sitemap_generate',
            [
                'sitemapIds' => $sitemapIds
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getDisplayMessage()
    {
        return 'Generated sitemap ids:';
    }

    /**
     * Retrieve finish notice
     *
     * @return string
     */
    protected function getSuccessMessage()
    {
        return 'Generation has been finished successfuly.';
    }

    /**
     * Perform cache management action
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ids   = $this->getRequestedIds($input);

        if (empty($input->getArgument("ids"))) {
            $output->writeln($this->getDisplayMessage());
            $output->writeln($this->getColumnsValues());
           // $output->writeln(join(PHP_EOL, $ids));
        } else {
            $this->performAction($ids);
            $output->writeln($this->getSuccessMessage());
           // $output->writeln(join(PHP_EOL, $ids));
        }
    }
}
