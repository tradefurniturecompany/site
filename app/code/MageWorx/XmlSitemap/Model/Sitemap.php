<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;

/**
 * {@inheritdoc}
 */
class Sitemap extends AbstractModel
{

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var GeneratorManager
     */
    protected $generator;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $directory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Sitemap constructor.
     * @param DateTime $date
     * @param GeneratorManager $generator
     * @param Filesystem $filesystem
     * @param Context $context
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        DateTime $date,
        GeneratorManager $generator,
        Filesystem $filesystem,
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeManager        = $storeManager;
        $this->date = $date;
        $this->generator = $generator;
        $this->filesystem = $filesystem;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init('MageWorx\XmlSitemap\Model\ResourceModel\Sitemap');
    }

    /**
     * Generate sitemap xml file
     */
    public function generateXml() {
        $this->generator->generateXml($this);

        $this->setSitemapTime($this->date->gmtDate());
        $this->setCountByEntity($this->convertCountByEntityDataToString());
        $this->resetCountByEntity();

        $this->save();
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $realPath = $this->getSitemapPath();

        if (!$this->getStoreId()) {
            throw new LocalizedException(__('Please choose store view'));
        }

        if (!$realPath && preg_match('#\.\.[\\\/]#', $realPath)) {
            throw new LocalizedException(__('Please define correct path'));
        }

        if (!$this->directory->isExist($realPath, false)) {
            throw new LocalizedException(
                __(
                    'Please create the specified folder "%1" before saving the sitemap.',
                    $this->getSitemapPath()
                )
            );
        }

        if (!$this->directory->isWritable($realPath)) {
            throw new LocalizedException(
                __(
                    'Please make sure that "%1" is writable by web-server.',
                    $this->getSitemapPath()
                )
            );
        }

        if (!preg_match('#^[a-zA-Z0-9_\.]+$#', $this->getSitemapFilename())) {
            throw new LocalizedException(__('Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in the filename. No spaces or other characters are allowed.'));
        }

        if (!preg_match('#\.xml$#', $this->getSitemapFilename())) {
            $this->setSitemapFilename($this->getSitemapFilename() . '.xml');
        }

        $this->checkFileNameIsOriginal();

        return parent::beforeSave();
    }

    /**
     * @return $this
     */
    public function beforeDelete()
    {
        $this->removeFiles();
        return parent::beforeDelete();
    }

    /**
     * @return void
     */
    function removeFiles()
    {
        if ($this->getSitemapFilename() && file_exists($this->getFullPathFilename())) {
            $filePathNames = array($this->getFullPathFilename());

            $fileNames = $this->getFileNamesFromSitemapIndex();

            foreach ($fileNames as $fileName) {
                $filePathNames[] = $this->getFullPathFilename($fileName);
            }

            foreach ($filePathNames as $fullPathName) {
                if (file_exists($fullPathName)) {
                    unlink($fullPathName);
                }
            }
        }
    }

    /**
     * @throws LocalizedException
     */
    protected function checkFileNameIsOriginal()
    {
        $otherFilePath = $this->getOtherModelFullPathFilenames();
        $alreadyIs     = array_search($this->getFullPathFilename(), $otherFilePath);
        if ($alreadyIs) {
            $mes = "The file with such name '%1' exists (sitemap id = %2). Please, select other name for the file.";
            throw new LocalizedException(__($mes, $otherFilePath[$alreadyIs], $alreadyIs));
        }
    }

    /**
     * @return array
     */
    protected function getOtherModelFullPathFilenames()
    {
        $models    = $this->getCollection()->addFieldToFilter('sitemap_id', array('nin' => array($this->getSitemapId())))->getItems();
        $filepaths = array();
        foreach ($models as $model) {
            $filepaths[$model->getSitemapId()] = $this->getFullPathFilename(
                $model->getSitemapFilename(),
                $model->getSitemapPath()
            );
        }

        return $filepaths;
    }

    /**
     * @param bool $sitemapFilename
     * @param bool $sitemapPath
     * @return string
     */
    public function getFullPathFilename($sitemapFilename = false, $sitemapPath = false)
    {
        if (!$sitemapFilename) {
            $sitemapFilename = $this->getSitemapFilename();
        }

        return $this->getFullPath($sitemapPath) . $sitemapFilename;
    }

    /**
     * @param bool $sitemapPath
     * @return string
     */
    public function getFullPath($sitemapPath = false)
    {
        if (!$sitemapPath) {
            $sitemapPath = $this->getSitemapPath();
        }

        $sitemapPath = trim($sitemapPath, '/');

        return $sitemapPath ? $this->getBaseDir() . $sitemapPath . '/' :
            $this->getBaseDir();
    }

    /**
     * @param bool $fullPathFilename
     * @return array
     */
    protected function getFileNamesFromSitemapIndex($fullPathFilename = false)
    {
        $fileNames = [];

        if (!$fullPathFilename) {
            $fullPathFilename = $this->getFullPathFilename();
        }

        $sxml = simplexml_load_file($fullPathFilename);

        if ($sxml) {
            $i = 0;

            while (@$sxml->sitemap[$i]->loc instanceof \SimpleXMLElement) {
                $el      = $sxml->sitemap[$i]->loc;
                $fileUrl = $el->__toString();
                if ($fileUrl != "" && preg_match('/_([0-9]){3}.xml$/', $fileUrl)) {
                    $urlParts    = explode("/", $fileUrl);
                    $fileName    = array_pop($urlParts);
                    $fileNames[] = $fileName;
                }

                $i++;
            }
        }
        return $fileNames;
    }

    /**
     * Get base dir
     *
     * @return string
     */
    protected function getBaseDir()
    {
        return $this->directory->getAbsolutePath();
    }

    /**
     * @param $realPath
     * @return string
     */
    protected function prepareSitemapPath($realPath)
    {
        return rtrim(str_replace(str_replace('\\', '/', $this->getBaseDir()), '', $realPath), '/');
    }

    /**
     * @return string
     */
    public function getFullTempPath()
    {
        return $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath();
    }

    /**
     * add countByEntity array to sitemap model
     */
    protected function convertCountByEntityDataToString() {
        $data = $this->generator->getCountByEntity();

        $text = '';

        foreach ($data as $item) {

            if (!$item['title'] && !$item['value']) {
                continue;
            }

            $text .= $item['title'] . ' - ' . $item['value'] . '; ';
        }

        return $text;
    }

    /**
     * @param string $type
     * @return string
     */
    public function getStoreBaseUrl($type = \Magento\Framework\UrlInterface::URL_TYPE_LINK)
    {
        $store = $this->storeManager->getStore($this->getStoreId());
        $isSecure = $store->isUrlSecure();

        $basePart = rtrim($store->getBaseUrl($type, $isSecure), '/') . '/';

        return $basePart;
    }

    /**
     * Reset the number of links in sitemap by entity type
     */
    public function resetCountByEntity()
    {
        $this->generator->resetCountByEntity();
    }
}
