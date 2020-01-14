<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model;

use MageWorx\SeoReports\Api\Data\PageReportInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Page Report Model
 */
class PageReport extends AbstractModel implements PageReportInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_page_report';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\SeoReports\Model\ResourceModel\PageReport');
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return parent::getData(self::REPORT_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function getRelationId()
    {
        return parent::getData(self::REFERENCE_ID);
    }

    /**
     * Get store ID
     *
     * @return int
     */
    public function getStoreId()
    {
        return parent::getData(self::STORE_ID);
    }

    /**
     * Get URL path
     *
     * @return string
     */
    public function getUrlPath()
    {
        return parent::getData(self::URL_PATH);
    }

    /**
     * Get heading
     *
     * @return string
     */
    public function getHeading()
    {
        return parent::getData(self::HEADING);
    }

    /**
     * Get prepared heading
     *
     * @return string
     */
    public function getPreparedHeading()
    {
        return parent::getData(self::PREPARED_HEADING);
    }

    /**
     * Get heading length
     *
     * @return int
     */
    public function getHeadingLength()
    {
        return parent::getData(self::HEADING_LENGTH);
    }

    /**
     * Get count of duplicated headings
     *
     * @return int
     */
    public function getHeadingDuplicateCount()
    {
        return parent::getData(self::HEADING_DUPLICATE_COUNT);
    }

    /**
     * Get meta title
     *
     * @return string|null
     */
    public function getMetaTitle()
    {
        return parent::getData(self::META_TITLE);
    }

    /**
     * Get prepared meta title
     *
     * @return string|null
     */
    public function getPreparedMetaTitle()
    {
        return parent::getData(self::PREPARED_META_TITLE);
    }

    /**
     * Get meta title length
     *
     * @return int
     */
    public function getMetaTitleLength()
    {
        return parent::getData(self::META_TITLE_LENGTH);
    }

    /**
     * Get count of duplicated meta titles
     *
     * @return int
     */
    public function getMetaTitleDuplicateCount()
    {
        return parent::getData(self::META_TITLE_DUPLICATE_COUNT);
    }

    /**
     * Get meta description length
     *
     * @return int
     */
    public function getMetaDescriptionLength()
    {
        return parent::getData(self::META_DESCRIPTION_LENGTH);
    }

    /**
     * Get URL path length
     *
     * @return int
     */
    public function getUrlPathLength()
    {
        return parent::getData(self::URL_PATH_LENGTH);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setId($id)
    {
        return $this->setData(self::REPORT_ID, $id);
    }

    /**
     * Set relation(page) ID
     *
     * @param int $pageId
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setRelationId($pageId)
    {
        return $this->setData(self::REFERENCE_ID, $pageId);
    }

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Set URL path
     *
     * @param string $urlPath
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setUrlPath($urlPath)
    {
        return $this->setData(self::URL_PATH, $urlPath);
    }

    /**
     * Set heading
     *
     * @param string $heading
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setHeading($heading)
    {
        return $this->setData(self::HEADING, $heading);
    }

    /**
     * Set prepared heading
     *
     * @param string $preparedHeading
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setPreparedHeading($preparedHeading)
    {
        return $this->setData(self::PREPARED_HEADING, $preparedHeading);
    }

    /**
     * Set heading length
     *
     * @param int $length
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setHeadingLength($length)
    {
        return $this->setData(self::HEADING_LENGTH, $length);
    }

    /**
     * Set count of duplicated headings
     *
     * @param int $count
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setHeadingDuplicateCount($count)
    {
        return $this->setData(self::HEADING_DUPLICATE_COUNT, $count);
    }

    /**
     * Set meta title
     *
     * @param string $title
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setMetaTitle($title)
    {
        return $this->setData(self::META_TITLE, $title);
    }

    /**
     * Set prepared meta title
     *
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setPreparedMetaTitle($preparedTitle)
    {
        return $this->setData(self::PREPARED_META_TITLE, $preparedTitle);
    }

    /**
     * Set meta title length
     *
     * @param int $length
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setMetaTitleLength($length)
    {
        return $this->setData(self::META_TITLE_LENGTH, $length);
    }

    /**
     * Set count of duplicated meta titles
     *
     * @type int $count
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setMetaTitleDuplicateCount($count)
    {
        return $this->setData(self::META_TITLE_DUPLICATE_COUNT, $count);
    }

    /**
     * Set meta description length
     *
     * @param int $length
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setMetaDescriptionLength($length)
    {
        return $this->setData(self::META_DESCRIPTION_LENGTH, $length);
    }

    /**
     * Set URL path length
     *
     * @type int $length
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setUrlPathLength($length)
    {
        return $this->setData(self::URL_PATH_LENGTH, $length);
    }
}
