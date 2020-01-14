<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model;

/**
 * {@inheritdoc}
 */
interface WriterInterface
{
    /**
     * @param $filePath
     * @param $fileName
     * @param $tempFilePath
     * @param $storeBaseUrl
     * @param $storeId
     * @return mixed
     */
    public function init($filePath, $fileName, $tempFilePath, $storeBaseUrl, $storeId);

    /**
     * @param $rawUrl
     * @param $lastmod
     * @param $changefreq
     * @param $priority
     * @param $imageUrls
     * @param $alternateUrls
     * @return mixed
     */
    public function write($rawUrl, $lastmod, $changefreq, $priority, $imageUrls, $alternateUrls);

    /**
     * Write header
     */
    public function startWriteXml();

    /**
     * Close file and generate index file
     */
    public function endWriteXml();

    /**
     * @return int
     */
    public function getWritedImages();
}