<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Plugin;

use Magento\Framework\Event\ManagerInterface as EventManagerInterface;

/**
 * {@inheritdoc}
 */
class CronGenerateSitemap
{
    /** @var EventManagerInterface */
    protected $eventManager;

    public function __construct(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    public function aroundScheduledGenerateSitemaps(\Magento\Sitemap\Model\Observer $subject, callable $proceed)
    {
        $this->eventManager->dispatch('mageworx_xmlsitemap_sitemap_generate');
    }
}