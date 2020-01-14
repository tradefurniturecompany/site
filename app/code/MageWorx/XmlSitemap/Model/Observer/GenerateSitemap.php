<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model\Observer;

use MageWorx\XmlSitemap\Model\ResourceModel\Sitemap\CollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MageWorx\XmlSitemap\Helper\MagentoSitemap;
use \Magento\Framework\Mail\Template\TransportBuilder;
use \Magento\Framework\Translate\Inline\StateInterface;
/**
 * Observer class for product template apply proccess
 */
class GenerateSitemap implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     *
     * @var \MageWorx\XmlSitemap\Model\ResourceModel\Sitemap\CollectionFactory
     */
    protected $sitemapCollectionFactory;

    /**
     * @var \MageWorx\XmlSitemap\Helper\MagentoSitemap;
     */
    protected $helperMagentoSitemap;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * GenerateSitemap constructor.
     * @param DateTime $date
     * @param CollectionFactory $sitemapCollectionFactory
     * @param MagentoSitemap $helperMagentoSitemap
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        DateTime $date,
        CollectionFactory $sitemapCollectionFactory,
        MagentoSitemap $helperMagentoSitemap,
        \Magento\Framework\App\State $state,
        TransportBuilder $transportBuilder
    ) {

        $this->date = $date;
        $this->sitemapCollectionFactory = $sitemapCollectionFactory;
        $this->helperMagentoSitemap = $helperMagentoSitemap;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * Apply product template
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $this->sitemapCollectionFactory->create();
        $generationErrors = [];

        if ($ids = $observer->getData('sitemapIds')) {
            $collection->loadByIds($ids);
        }

        foreach ($collection as $sitemap) {
            try {
                $sitemap->generateXml();

            } catch (\Exception $e) {
                    $generationErrors[] = $e->getMessage();
            }
        }

        if ($generationErrors && $this->helperMagentoSitemap->getErrorRecipient()
        ) {
            $header = $this->getEmailHeader($observer);
            array_unshift($generationErrors, $header);
            $this->transportBuilder->setTemplateIdentifier(
                $this->helperMagentoSitemap->getErrorEmailTemplate()
            )->setTemplateOptions(
                [
                    'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )->setTemplateVars(
                ['warnings' => join("\n", $generationErrors)]
            )->setFrom(
                $this->helperMagentoSitemap->getErrorIdentity()
            )->addTo(
                $this->helperMagentoSitemap->getErrorRecipient()
            );
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
        }
    }

    protected function getEmailHeader($observer)
    {
        $string = __('Unfortunately, the process of generating sitemap(s) went wrong.');
        $string .= ' ' . __('Module') . ': MageWorx_XmlSitemap, ' . __('Date') . ': ' . date("Y-m-d") . '. ';
        $string .= __('Sitemap(s) with id(s)  %1 were not updated.', implode($observer->getData('sitemapIds'), ", "));
        return $string;
    }
}
