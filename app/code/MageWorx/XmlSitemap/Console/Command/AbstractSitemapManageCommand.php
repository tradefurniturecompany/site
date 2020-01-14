<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManagerInterface;
use MageWorx\XmlSitemap\Model\ResourceModel\Sitemap\CollectionFactory;
use Magento\Framework\App\State;

abstract class AbstractSitemapManageCommand extends Command
{
    /**
     * Input argument types
     */
    const INPUT_KEY_IDS = 'ids';

    /** @var EventManagerInterface */
    protected $eventManager;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /**
     * @var State
     */
    protected $appState;

    /**
     *
     * @var \MageWorx\XmlSitemap\Model\ResourceModel\Sitemap\CollectionFactory
     */
    protected $sitemapCollectionFactory;

    /**
     * AbstractSitemapManageCommand constructor.
     * @param Manager $manager
     * @param EventManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $sitemapCollectionFactory
     */
    public function __construct(
        EventManagerInterface $eventManager,
        CollectionFactory $sitemapCollectionFactory,
        StoreManagerInterface $storeManager,
        State $appState
    ) {
        $this->storeManager = $storeManager;
        $this->eventManager = $eventManager;
        $this->sitemapCollectionFactory = $sitemapCollectionFactory;
        $this->appState = $appState;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument(
            self::INPUT_KEY_IDS,
            InputArgument::IS_ARRAY,
            'Space-separated list of sitemap ids or omit to apply to all sitemap ids.'
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
        try {
            $this->appState->getAreaCode();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->appState->setAreaCode('adminhtml');
        }

        $requestedIds = [];
        if ($input->getArgument(self::INPUT_KEY_IDS)) {
            $requestedIds = $input->getArgument(self::INPUT_KEY_IDS);
            $requestedIds = array_filter(array_map('trim', $requestedIds), 'strlen');
        }

        if (empty($requestedIds)) {
            return $this->getAvailableIds();
        } else {
            $availableIds = $this->getAvailableIds();
            $unsupportedIds = array_diff($requestedIds, $availableIds);
            if ($unsupportedIds) {
                throw new \InvalidArgumentException(
                    "The following requested sitemap ids are not supported: '" . join("', '", $unsupportedIds)
                    . "'." . PHP_EOL . 'Supported ids: ' . join(", ", $availableIds)
                );
            }
            return array_values(array_intersect($availableIds, $requestedIds));
        }
    }

    /**
     * @return array
     */
    public function getAvailableIds()
    {
        /** @var \MageWorx\XmlSitemap\Model\ResourceModel\Sitemap\Collection */
        $collection = $this->sitemapCollectionFactory->create();

        return $collection->getAllIds();
    }

    /**
     * @return array
     */
    public function getColumnsValues()
    {
        /** @var \MageWorx\XmlSitemap\Model\ResourceModel\Sitemap\Collection */
        $collection = $this->sitemapCollectionFactory->create();

        $col = [];
        foreach ($collection->getItems() as $item) {
            $col[] = $item->getData('sitemap_id').' - '.
                $item->getData('sitemap_filename').' - '.
                $this->storeManager->getStore($item->getData('store_id'))->getName();
        }
        return $col;
    }
}