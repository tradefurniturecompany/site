<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\XmlSitemap\Helper\Data as Helper;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\Filesystem\Io\File;
use \Zend\Validator\Sitemap\Changefreq as ChangefreqValidator;
use \Zend\Validator\Sitemap\Lastmod as LastmodValidator;
use \Zend\Validator\Sitemap\Loc as LocationValidator;
use \Zend\Validator\Sitemap\Priority as PriorityValidator;
use \Magento\Store\Model\Store as StoreModel;

/**
 * {@inheritdoc}
 */
class Writer implements WriterInterface
{
    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected  $fileDir;

    /**
     * @var string
     */
    protected $tempFilePath;

    /**
     * @var bool
     */
    protected $hasImagesLink    = true;

    /**
     * @var bool
     */
    protected $hasAlternateLink = true;

    /**
     * @var int
     */
    protected $useIndex      = 5;

    /**
     * @var int
     */
    protected $maxLinks      = 50000;

    /**
     * @var int
     */
    protected $splitSize     = 10000000;

    /**
     * @var int
     */
    protected $sitemapInc    = 1;

    /**
     * @var int
     */
    protected $currentInc    = 0;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var File
     */
    protected $io;

    /**
     * @var bool
     */
    protected $init          = false;

    /**
     * @var int
     */
    public $imageCount;

    /**
     *
     * @var type string
     */
    public $storeBaseUrl;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var WriteInterface
     */
    protected $directory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Writer constructor.
     * @param Helper $helper
     * @param DateTime $date
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @param File $io
     * @param ChangefreqValidator $changefreqValidator
     * @param LastmodValidator $lastmodValidator
     * @param LocationValidator $locationValidator
     * @param PriorityValidator $priorityValidator
     */
    public function __construct(
        Helper $helper,
        DateTime $date,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        File $io,
        ChangefreqValidator $changefreqValidator,
        LastmodValidator $lastmodValidator,
        LocationValidator $locationValidator,
        PriorityValidator $priorityValidator
    ) {
        $this->helper       = $helper;
        $this->date         = $date;
        $this->io           = $io;
        $this->storeManager = $storeManager;
        $this->directory    = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->changefreqValidator = $changefreqValidator;
        $this->lastmodValidator    = $lastmodValidator;
        $this->locationValidator   = $locationValidator;
        $this->priorityValidator   = $priorityValidator;
    }

    /**
     * @param string $filePath
     * @param string $fileDir
     * @param string $fileName
     * @param string $tempFilePath
     * @param bool $storeBaseUrl
     * @param int $storeId
     * @return mixed|void
     * @throws LocalizedException
     */
    public function init(
        $filePath,
        $fileDir,
        $fileName,
        $tempFilePath,
        $storeBaseUrl = false,
        $storeId = StoreModel::DEFAULT_STORE_ID
    ) {
        $this->filePath       = $filePath;
        $this->fileDir        = $fileDir;
        $this->fileName       = $fileName;
        $this->tempFilePath   = $tempFilePath;
        $this->imageCount     = 0;
        $this->sitemapInc     = 1;
        $this->currentInc     = 0;

        $this->storeManager->setCurrentStore($storeId);

        $this->loadParamsFromConfig();

        if ($this->useIndex && !$storeBaseUrl) {
            throw new LocalizedException(__('The sitemap index file can\'t be created without storeBaseUrl . Process is canceled.'));
        }
        else {
            $this->storeBaseUrl = $storeBaseUrl;
        }

        $this->openXml();
        $this->init = true;
    }

    /**
     * Load params from config
     */
    protected function loadParamsFromConfig()
    {
        $hasAlternateLink = $this->helper->isAlternateUrlsEnabled();
        if (!empty($hasAlternateLink)) {
            $this->hasAlternateLink = $hasAlternateLink;
        }

        $this->useIndex = true;

        $splitSize = $this->helper->getSplitSize();
        if (!empty($splitSize)) {
            $this->splitSize = $splitSize;
        }

        $maxLinks = $this->helper->getMaxLinks();
        if (!empty($maxLinks)) {
            $this->maxLinks = $maxLinks;
        }
    }

    /**
     * @param string $rawUrl
     * @param string $lastmod
     * @param string $changefreq
     * @param string $priority
     * @param \Magento\Framework\DataObject|false $imageUrls
     * @param array|false $alternateUrls
     * @throws LocalizedException
     */
    public function write($rawUrl, $lastmod, $changefreq, $priority, $imageUrls = false, $alternateUrls = false)
    {
        if (!$this->init) {
            throw new LocalizedException(__('Sitemap Writer class wasn\'t initialized.'));
        }

        $url = htmlspecialchars($rawUrl);
        $this->isInputDataValid($url, $lastmod, $changefreq, $priority, $imageUrls);

        $imagePartXml         = "";
        $alternateUrlsPartXml = "";

        $countAdditionalLinks = 0;
        if ($imageUrls) {
            $imageCount = count($imageUrls->getCollection());
            $countAdditionalLinks += $imageCount;

            $imagePartXml .= $this->getImageXml($imageUrls);
        }

        if ($this->hasAlternateLink) {
            if (is_array($alternateUrls) && count($alternateUrls) > 0) {
                $countAdditionalLinks += count($alternateUrls);
                foreach ($alternateUrls as $hreflang => $altUrl) {
                    $alternateUrlsPartXml .= '<xhtml:link rel="alternate" hreflang="' . $hreflang . '" href="' . $altUrl . '"/>';
                }
            }
        }

        $this->checkSitemapLimits($countAdditionalLinks);

        $xml = sprintf(
            '<url><loc>%s</loc>%s<lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority>%s</url>',
            $url, $alternateUrlsPartXml, $lastmod, $changefreq, $priority, $imagePartXml
        );

        $this->stream->write($xml);
    }

    /**
     *
     * @param array $images
     * @return string
     */
    protected function getImageXml($images)
    {
        $xml = '';
        foreach ($images->getCollection() as $image) {

            $this->imageCount++;
            $preparedImageUrl     = htmlspecialchars($this->getMediaUrl($image->getUrl()));
            $preparedThumbnailUrl = htmlspecialchars($this->getMediaUrl($images->getThumbnail()));
            $preparedTitle        = htmlspecialchars($images->getTitle());
            $preparedCaption      = $image->getCaption() ? htmlspecialchars($image->getCaption()) : '';

            $xmlImage = $this->getWrappedString($preparedImageUrl, 'image:loc');
            $xmlImage .= $this->getWrappedString($preparedTitle, 'image:title');
            if ($preparedCaption) {
                $xmlImage .= $this->getWrappedString($preparedCaption, 'image:caption');
            }

            $xml .= $this->getWrappedString($xmlImage, 'image:image');
        }
        if ($xml) {
            $this->imageCount++;

            $xml .= '<PageMap xmlns="http://www.google.com/schemas/sitemap-pagemap/1.0"><DataObject type="thumbnail">';
            $xml .= '<Attribute name="name" value="' . $preparedTitle . '"/>';
            $xml .= '<Attribute name="src" value="' . $preparedThumbnailUrl . '"/>';
            $xml .= '</DataObject></PageMap>';
        }
        return $xml;
    }

    /**
     * @param $url
     * @return string
     */
    protected function getMediaUrl($url)
    {
        $storeBaseUrl = $this->getStoreBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return  strripos($url, $storeBaseUrl) === false ? $storeBaseUrl . ltrim($url, '/') : $url;
    }

    /**
     * Get store base url
     *
     * @param string $type
     * @return string
     */
    protected function getStoreBaseUrl($type = \Magento\Framework\UrlInterface::URL_TYPE_WEB)
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();

        $isSecure = $store->isUrlSecure();

        return rtrim($store->getBaseUrl($type, $isSecure), '/') . '/';
    }

    /**
     *
     * @param string $string
     * @param string $tagName
     * @return string
     */
    protected function getWrappedString($string, $tagName)
    {
        return '<' . $tagName . '>' . $string . '</' . $tagName . '>';
    }

    /**
     * @param $url
     * @param $lastmod
     * @param $changefreq
     * @param $priority
     * @throws LocalizedException
     */
    protected function isInputDataValid($url, $lastmod, $changefreq, $priority)
    {

        if ($this->locationValidator->isValid($url) == false && $this->helper->isEnableValidateUrls()) {
            throw new LocalizedException(__("Location value '%1' is not valid.", $url));
        }

        if ($this->changefreqValidator->isValid($changefreq) == false) {
            throw new LocalizedException(__("Changefreq value '%1' is not valid. Item url: '%2'.", $changefreq, $url));
        }

        if ($this->lastmodValidator->isValid($lastmod) == false) {
            throw new LocalizedException(__("Lastmod value '%1' is not valid. Item url: '%2'.", $lastmod, $url));
        }

        if ($this->priorityValidator->isValid($priority) == false) {
            throw new LocalizedException( __("Priority value '%1' is not valid. Item url: '%2'.", $priority, $url));
        }
    }

    /**
     * @throws LocalizedException
     */
    protected function openPathAndFileExist()
    {
        $filePath = $this->filePath;
        $fileName = $this->getSitemapFilename();

        $this->stream = $this->directory->openFile( $fileName, 'a+');
    }

    /**
     * Write header
     */
    public function startWriteXml()
    {
        $this->openXml(true);
    }

    /**
     * Close file and generate index file
     */
    public function endWriteXml()
    {
        if ($this->init) {
            $this->closeXml();

            if ($this->sitemapInc == 1) {
                $path        = $this->filePath . $this->getSitemapFilename();
                $destination = $this->filePath . $this->fileName;

                $result = $this->io->mv($path, $destination);

                if (!$result) {
                    throw new LocalizedException(
                        __("The following file renaming from: file %1 into %2 is impossible.", $path, $destination)
                    );
                }
            } else {
                $this->generateSitemapIndex();
            }
        }
    }

    /**
     * @param bool $headerWrite
     */
    protected function openXml($headerWrite = false)
    {
        $this->openPathAndFileExist();
        $this->stream = $this->directory->openFile($this->getSitemapFilename(), 'w+');
        if ($headerWrite) {
            $this->writeXmlHeader();
        }
    }

    /**
     * Write header in xml file
     */
    protected function writeXmlHeader()
    {
        $this->stream->write(
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        );

        $add = "";

        $add .= "\n" . ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';

        if ($this->hasAlternateLink) {
            $add .= "\n" . ' xmlns:xhtml="http://www.w3.org/1999/xhtml"';
        }

        $this->stream->write( '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . $add .'>');
    }

    /**
     * @param int $countAdditionalLinks
     */
    protected function checkSitemapLimits($countAdditionalLinks = 0)
    {
        if ($this->useIndex) {
            if ($this->currentInc + $countAdditionalLinks >= $this->maxLinks) {
                $this->currentInc = 0;
                $this->closeXml();
                $this->sitemapInc++;
                $this->openXml(true);
            }

            $this->currentInc += 1 + $countAdditionalLinks;
        }
    }

    /**
     * @return string
     */
    protected function getSitemapFilename()
    {
        if ($this->useIndex) {
            $sitemapFilename = $this->fileName;
            $ext             = strrchr($sitemapFilename, '.');
            $sitemapFilename = substr($sitemapFilename, 0, strlen($sitemapFilename) - strlen($ext)) . '_' . sprintf(
                    '%03s',
                    $this->sitemapInc
                ) . $ext;
            return $sitemapFilename;
        }

        return trim($this->fileName, '/');
    }

    /**
     * close xml file
     */
    public function closeXml()
    {
        $this->stream = $this->directory->openFile($this->getSitemapFilename(), 'a+');
        $this->stream->write('</urlset>');
        $this->stream->close();

        $this->moveFileFromTempToOriginal();
    }

    /**
     * @param bool $fileName
     * @throws LocalizedException
     */
    protected function moveFileFromTempToOriginal($fileName = false)
    {
        if (!$fileName) {
            $fileName = $this->getSitemapFilename();
        }

        $from   = $this->tempFilePath . $fileName;
        $to     = $this->filePath . $fileName;
        $result = $this->io->mv($from, $to);
        if (!$result) {
            throw new LocalizedException(__("Relocation of the file %1 to %2 is impossible.", $from, $to));
        }
    }

    /**
     * generate indexfile
     */
    protected function generateSitemapIndex()
    {
        if (!$this->useIndex) {
            return;
        }

        $this->openPathAndFileExist();

        $this->stream = $this->directory->openFile($this->fileName, 'w+');
        $this->stream->write('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        $this->stream->write('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');

        $date = $this->date->gmtDate('Y-m-d');
        $i    = $this->sitemapInc;

        for ($this->sitemapInc = 1; $this->sitemapInc <= $i; $this->sitemapInc++) {
            $fileName = $this->getSitemapFilename();
            $xml = sprintf(
                '<sitemap><loc>%s</loc><lastmod>%s</lastmod></sitemap>',
                htmlspecialchars(rtrim($this->getStoreBaseUrl(), '/') . $this->fileDir . $fileName), $date
            );
            $this->stream->write($xml);
        }

        $this->sitemapInc = $i;

        $this->stream->write('</sitemapindex>');
        $this->stream->close();

        $this->moveFileFromTempToOriginal($this->fileName);
    }

    /**
     * @return int
     */
    public function getWritedImages()
    {
        return $this->imageCount;
    }
}