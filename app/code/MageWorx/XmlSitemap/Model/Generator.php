<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model;

use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use MageWorx\XmlSitemap\Helper\Data as Helper;
use Magento\Framework\ObjectManagerInterface;
use MageWorx\XmlSitemap\Model\Generator\AbstractGenerator;
use Magento\Framework\DataObject;

/**
 * {@inheritdoc}
 */
class Generator extends AbstractGenerator
{
    /** @var EventManagerInterface */
    protected $eventManager;

    /**
     * @var int
     */
    protected $counter = 0;

    /**
     * Generator constructor.
     * @param Helper $helper
     * @param ObjectManagerInterface $objectManager
     * @param EventManagerInterface $eventManager
     */
    public function __construct(
        Helper $helper,
        ObjectManagerInterface $objectManager,
        EventManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
        parent::__construct($helper, $objectManager);
    }

    /**
     * @param $storeId
     * @param $writer
     */
    public function generate($storeId, $writer)
    {
        $this->storeId = $storeId;
        $this->helper->init($this->storeId);
        $this->storeBaseUrl = $writer->storeBaseUrl;
        $container = new DataObject();
        $container->setGenerators([]);
        $eventArgs = [
            'storeId' => $storeId,
            'container' => $container
        ];

        $this->eventManager->dispatch(
            'mageworx_xmlsitemap_add_generator',
            $eventArgs
        );

        $container = $eventArgs['container'];
        foreach ($container->getGenerators() as $generatorName => $generatorData) {
            if (empty($generatorData['items'])) {
                continue;
            }

            $this->code = $generatorName;
            $this->name .= empty($generatorData['title']) ? '' : $generatorData['title'] . '; ';
            $priority = empty($generatorData['priority']) ?
                $this->helper->getPagePriority($storeId) : $generatorData['priority'];
            $changefreq = empty($generatorData['changefreq']) ?
                $this->helper->getPageChangefreq($storeId) : $generatorData['changefreq'];

            foreach ($generatorData['items'] as $item) {
                if (empty($item['url_key'])) {
                    continue;
                }
                $this->counter++;
                $urlKey = $this->getItemUrl($item['url_key']);

                $dateCanged = empty($item['date_changed']) ?
                    $this->helper->getCurrentDate() : $item['date_changed'];

                $writer->write(
                    $urlKey,
                    $dateCanged,
                    $changefreq,
                    $priority
                );
            }
            unset($generatorData['items']);
        }
    }

    /**
     * @param $urlKey
     * @return string
     */
    protected function getItemUrl($urlKey)
    {
        return $this->helper->trailingSlash($this->storeBaseUrl . $urlKey);
    }
}