<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model\Rewrite;

/**
 * {@inheritdoc}
 */
class Sitemap extends \Magento\Sitemap\Model\Sitemap
{

    /**
     * Real file path
     *
     * @var string
     */
    protected $_filePath;

    /**
     * Sitemap items
     *
     * @var array
     */
    protected $_sitemapItems = [];

    /**
     * Current sitemap increment
     *
     * @var int
     */
    protected $_sitemapIncrement = 0;

    /**
     * Sitemap start and end tags
     *
     * @var array
     */
    protected $_tags = [];

    /**
     * Number of lines in sitemap
     *
     * @var int
     */
    protected $_lineCount = 0;

    /**
     * Current sitemap file size
     *
     * @var int
     */
    protected $_fileSize = 0;

    /**
     * New line possible symbols
     *
     * @var array
     */
    private $_crlf = ["win" => "\r\n", "unix" => "\n", "mac" => "\r"];

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $_directory;

    /**
     * @var \Magento\Framework\Filesystem\File\Write
     */
    protected $_stream;

    /**
     * Sitemap data
     *
     * @var \Magento\Sitemap\Helper\Data
     */
    protected $_sitemapData;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Sitemap\Model\ResourceModel\Catalog\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Sitemap\Model\ResourceModel\Cms\PageFactory
     */
    protected $_cmsFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateModel;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    protected $mageworxOutFlag = false;

    protected $helperData;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Sitemap\Helper\Data $sitemapData
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory $categoryFactory
     * @param \Magento\Sitemap\Model\ResourceModel\Catalog\ProductFactory $productFactory
     * @param \Magento\Sitemap\Model\ResourceModel\Cms\PageFactory $cmsFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $modelDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \MageWorx\XmlSitemap\Helper\Data $helperData,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Escaper $escaper,
        \Magento\Sitemap\Helper\Data $sitemapData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory $categoryFactory,
        \Magento\Sitemap\Model\ResourceModel\Catalog\ProductFactory $productFactory,
        \Magento\Sitemap\Model\ResourceModel\Cms\PageFactory $cmsFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $modelDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->helperData = $helperData;
        return parent::__construct(
            $context,
            $registry,
            $escaper,
            $sitemapData,
            $filesystem,
            $categoryFactory,
            $productFactory,
            $cmsFactory,
            $modelDate,
            $storeManager,
            $request,
            $dateTime,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Generate XML file
     *
     * @see http://www.sitemaps.org/protocol.html
     *
     * @return $this
     */
    public function generateXml()
    {
        if (!$this->helperData->isOptimizeHomePage($this->getStoreId()) && !$this->helperData->isShowLinks($this->getStoreId())) {
            $this->mageworxOutFlag = true;
            return parent::generateXml();
        }

        $this->_initSitemapItems();
        /** @var $sitemapItem \Magento\Framework\DataObject */
        foreach ($this->_sitemapItems as $sitemapItem) {
            $changefreq = $sitemapItem->getChangefreq();
            $priority = $sitemapItem->getPriority();
            $entityType = $sitemapItem->getEntityType();
            foreach ($sitemapItem->getCollection() as $item) {

                $isHomePage = false;
                if ($entityType == 'page') {
                    $isHomePage = $this->isHomePage($item->getUrl());
                }
                $xml = $this->_getSitemapRow(
                    $item->getUrl(),
                    $item->getUpdatedAt(),
                    $changefreq,
                    $priority,
                    $item->getImages(),
                    $entityType,
                    $isHomePage
                );

                if ($this->_sitemapIncrement > 0 && $this->_isSplitRequired($xml)) {
                    $this->finalizeSitemap();
                }
                if (!$this->_fileSize) {
                    $this->createSitemap();
                }
                $this->_writeSitemapRow($xml);
                $this->_fileSize  = $this->_fileSize + strlen($xml);
                $this->_lineCount = $this->_lineCount + 1;
            }
        }
        $this->finalizeSitemap();

        if ($this->_sitemapIncrement != 1) {
            $this->_createSitemapIndex();
        } else {
            $this->_directory->renameFile($this->_getPath(), $this->_getDestination());
        }

        $this->_beforeSaveProccess();
        $this->save();

        return $this;
    }

    /**
     * Initialize sitemap items
     *
     * @return void
     */
    protected function _initSitemapItems()
    {
        if ($this->mageworxOutFlag) {
            return parent::_initSitemapItems();
        }
        /** @var $helper \Magento\Sitemap\Helper\Data */
        $helper = $this->_sitemapData;
        $storeId = $this->getStoreId();

        $this->_sitemapItems[] = new \Magento\Framework\DataObject(
            [
                'entity_type' => 'category',
                'changefreq'  => $helper->getCategoryChangefreq($storeId),
                'priority'    => $helper->getCategoryPriority($storeId),
                'collection'  => $this->_categoryFactory->create()->getCollection($storeId),
            ]
        );

        $this->_sitemapItems[] = new \Magento\Framework\DataObject(
            [
                'entity_type' => 'product',
                'collection'  => $this->_productFactory->create()->getCollection($storeId),
                'priority'    => $helper->getProductPriority($storeId),
                'changefreq'  => $helper->getProductChangefreq($storeId),
            ]
        );

        $this->_sitemapItems[] = new \Magento\Framework\DataObject(
            [
                'entity_type' => 'page',
                'collection'  => $this->_cmsFactory->create()->getCollection($storeId),
                'priority'    => $helper->getPagePriority($storeId),
                'changefreq'  => $helper->getPageChangefreq($storeId),
            ]
        );

        if ($this->helperData->isShowLinks($this->getStoreId())) {
            $this->_sitemapItems[] = new \Magento\Framework\DataObject(
                [
                    'entity_type' => 'additional_link',
                    'collection'  => $this->helperData->getAdditionalLinkCollection($storeId),
                    'priority'    => $this->helperData->getAdditionalLinkPriority($storeId),
                    'changefreq'  => $this->helperData->getAdditionalLinkChangefreq($storeId),
                ]
            );
        }

        $this->_tags = [
            self::TYPE_URL => [
                self::OPEN_TAG_KEY => '<?xml version="1.0" encoding="UTF-8"?>' .
                PHP_EOL .
                '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' .
                ' xmlns:content="http://www.google.com/schemas/sitemap-content/1.0"' .
                ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' .
                PHP_EOL,
                self::CLOSE_TAG_KEY => '</urlset>',
            ],
            self::TYPE_INDEX => [
                self::OPEN_TAG_KEY => '<?xml version="1.0" encoding="UTF-8"?>' .
                PHP_EOL .
                '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
                PHP_EOL,
                self::CLOSE_TAG_KEY => '</sitemapindex>',
            ]
        ];
    }

    /**
     * Get sitemap row
     *
     * @param string $url
     * @param null|string $lastmod
     * @param null|string $changefreq
     * @param null|string $priority
     * @param null|array $images
     * @param string $entityType
     * @param boolean $isHomePage
     * @return string
     * Sitemap images
     * @see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=178636
     *
     * Sitemap PageMap
     * @see http://support.google.com/customsearch/bin/answer.py?hl=en&answer=1628213
     */
    protected function _getSitemapRow(
        $url,
        $lastmod = null,
        $changefreq = null,
        $priority = null,
        $images = null,
        $entityType = false,
        $isHomePage = false
    ) {

        if ($this->mageworxOutFlag) {
            return parent::_getSitemapRow($url, $lastmod, $changefreq, $priority, $images);
        }

        if ($entityType == 'additional_link') {
            $url = $this->convertAdditionalPageUrl($url);
        } elseif ($entityType == 'page') {
            if ($isHomePage) {
                $priority = '1.0';
                $url = $this->getClearHomePageUrl();
            } else {
                $url = $this->convertPageUrl($url);
            }
        } else {
            $url = $this->_getUrl($url);
        }

        $xml = $this->_getWrappedString(htmlspecialchars($url), 'loc');
        if ($lastmod) {
            $xml .= $this->_getWrappedString($this->_getFormattedLastmodDate($lastmod), 'lastmod');
        }
        if ($changefreq) {
            $xml .= $this->_getWrappedString($changefreq, 'changefreq');
        }
        if ($priority) {
            $xml .= sprintf($this->_getWrappedString('%.1f', 'priority'), $priority);
        }
        if ($images) {
            $xml .= $this->_getImageXml($images);
        }

        return $this->_getWrappedString($xml, 'url');
    }

    /**
     * Retrieve home page URL without CMS identifier with or without trailing slash
     *
     * @return string
     */
    protected function getClearHomePageUrl()
    {
        return $this->helperData->getTrailingSlashForHomePage() ? $this->_getUrl('') : rtrim($this->_getUrl(''), '/');
    }

    /**
     * Retrieve URL with or without trailing slash
     *
     * @param string $url
     * @return string
     */
    protected function convertPageUrl($url)
    {
        $cropeUrl = rtrim($this->_getUrl($url), '/');
        return $this->helperData->getTrailingSlash() ? $cropeUrl . '/' : $cropeUrl;
    }

    /**
     * Retrieve URL
     *
     * @param string $url
     * @return string
     */
    protected function convertAdditionalPageUrl($url)
    {
        if (strpos($url, '://') !== false) {
            return $url;
        }
        return $this->_getUrl($url);
    }

    /**
     * Check if home page URL
     *
     * @param string $url
     * @return bool
     */
    protected function isHomePage($url)
    {
        $homeIdentifier = $this->helperData->getHomeIdentifier($this->getStoreId());
        if (strpos($homeIdentifier, '|') !== false) {
            list($homeIdentifier, $homePageId) = explode('|', $homeIdentifier);
        }
        return ($homeIdentifier == $url);
    }

    /**
     * Create new sitemap file
     *
     * @param null|string $fileName
     * @param string $type
     * @return void
     */
    public function createSitemap($fileName = null, $type = self::TYPE_URL)
    {
        parent::_createSitemap($fileName, $type);
    }

    /**
     * Write closing tag and close stream
     *
     * @param string $type
     * @return void
     */
    public function finalizeSitemap($type = self::TYPE_URL)
    {
        parent::_finalizeSitemap($type);
    }

    /**
     *
     * @return string
     */
    protected function _getPath()
    {
        return rtrim($this->getSitemapPath(), '/') . '/' . $this->_getCurrentSitemapFilename($this->_sitemapIncrement);
    }

    /**
     *
     * @return string
     */
    protected function _getDestination()
    {
        return rtrim($this->getSitemapPath(), '/') . '/' . $this->getSitemapFilename();
    }

    /**
     * @return void
     */
    protected function _beforeSaveProccess()
    {
        if ($this->_isEnabledSubmissionRobots()) {
            $this->_addSitemapToRobotsTxt($this->getSitemapFilename());
        }

        $this->setSitemapTime($this->_dateModel->gmtDate('Y-m-d H:i:s'));
    }

    /**
     *
     * @param array $images
     * @return string
     */
    protected function _getImageXml($images)
    {
        $xml = '';
        foreach ($images->getCollection() as $image) {

            $preparedImageUrl     = htmlspecialchars($this->_getMediaUrl($image->getUrl()));
            $preparedThumbnailUrl = htmlspecialchars($this->_getMediaUrl($images->getThumbnail()));
            $preparedTitle        = htmlspecialchars($images->getTitle());
            $preparedCaption      = $image->getCaption() ? htmlspecialchars($image->getCaption()) : '';

            $xmlImage = $this->_getWrappedString($preparedImageUrl, 'image:loc');
            $xmlImage .= $this->_getWrappedString($preparedTitle, 'image:title');
            if ($preparedCaption) {
                $xmlImage .= $this->_getWrappedString($preparedCaption, 'image:caption');
            }

            $xml .= $this->_getWrappedString($xmlImage, 'image:image');
        }
        if ($xml) {
            $xml .= '<PageMap xmlns="http://www.google.com/schemas/sitemap-pagemap/1.0"><DataObject type="thumbnail">';
            $xml .= '<Attribute name="name" value="' . $preparedTitle . '"/>';
            $xml .= '<Attribute name="src" value="' . $preparedThumbnailUrl . '"/>';
            $xml .= '</DataObject></PageMap>';
        }
        return $xml;
    }

    /**
     *
     * @param string $string
     * @param string $tagName
     * @return string
     */
    protected function _getWrappedString($string, $tagName)
    {
        return '<' . $tagName . '>' . $string . '</' . $tagName . '>';
    }
}
