<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\XmlSitemap\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Link extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Column name
     */
    const NAME = 'link';

    /**
     * @var \Magento\Framework\Filesystem $filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Sitemap\Model\SitemapFactory
     */
    protected $sitemapFactory;

    /**
     * Link constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \MageWorx\XmlSitemap\Model\SitemapFactory $sitemapFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \MageWorx\XmlSitemap\Model\SitemapFactory $sitemapFactory,
        \Magento\Framework\Filesystem $filesystem,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->sitemapFactory = $sitemapFactory;
        $this->filesystem = $filesystem;
    }

    /**
    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                 $item['link'] = $this->getLink($item);
            }
        }
        return $dataSource;
    }

    /**
     * Prepare link to display in grid
     *
     * @param $item
     * @return string
     */
    public function getLink($item)
    {
        /** @var $sitemap \Mageworx\XmlSitemap\Model\Sitemap */
        $sitemap = $this->sitemapFactory->create();
        $fileName = preg_replace('/^\//', '', $item['sitemap_path'] . $item['sitemap_filename']);
        $directory = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
        $url = $sitemap->getStoreBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . $fileName;
        if ($directory->isFile($fileName)) {
            return  $url;
        }

        return '';
    }

    /**
     * Apply sorting
     *
     * @return void
     */
    protected function applySorting()
    {
        $sorting = $this->getContext()->getRequestParam('sorting');
        $isSortable = $this->getData('config/sortable');
        if ($isSortable !== false
            && !empty($sorting['field'])
            && !empty($sorting['direction'])
            && $sorting['field'] === $this->getName()
        ) {
            $this->getContext()->getDataProvider()->addOrder(
                $this->getName(),
                strtoupper($sorting['direction'])
            );
        }
    }
}
