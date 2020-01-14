<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model;

use MageWorx\SeoReports\Api\Data\ProductReportInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Product Report Model
 */
class ProductReport extends AbstractModel implements ProductReportInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_product_report';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\SeoReports\Model\ResourceModel\ProductReport');
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
     * Get product ID
     *
     * @return int
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return parent::getData(self::NAME);
    }

    /**
     * Get prepared name
     *
     * @return string
     */
    public function getPreparedName()
    {
        return parent::getData(self::PREPARED_NAME);
    }

    /**
     * Get count of duplicated names
     *
     * @return int
     */
    public function getNameDuplicateCount()
    {
        return parent::getData(self::NAME_DUPLICATE_COUNT);
    }

    /**
     * Get name length
     *
     * @return int
     */
    public function getNameLength()
    {
        return parent::getData(self::NAME_LENGTH);
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
     * Get product URL
     *
     * @return string
     */
    public function getUrl()
    {
        return parent::getData(self::URL);
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
     * @return \MageWorx\SeoReports\Api\Data\ProductReportInterface
     */
    public function setId($id)
    {
        return $this->setData(self::REPORT_ID, $id);
    }

    /**
     * Set product ID
     *
     * @param int $relationId
     * @return \MageWorx\SeoReports\Api\Data\ProductReportInterface
     */
    public function setRelationId($relationId)
    {
        return $this->setData(self::REFERENCE_ID, $relationId);
    }

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return \MageWorx\SeoReports\Api\Data\ProductReportInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Set URL path
     *
     * @param string $urlPath
     * @return \MageWorx\SeoReports\Api\Data\ProductReportInterface
     */
    public function setUrlPath($urlPath)
    {
        return $this->setData(self::URL_PATH, $urlPath);
    }

    /**
     * Set name
     *
     * @param string $name
     * @return \MageWorx\SeoReports\Api\Data\ProductReportInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Set prepared name
     *
     * @param string $preparedName
     * @return \MageWorx\SeoReports\Api\Data\ProductReportInterface
     */
    public function setPreparedName($preparedName)
    {
        return $this->setData(self::PREPARED_NAME, $preparedName);
    }

    /**
     * Set count of duplicated names
     *
     * @param int $count
     * @return \MageWorx\SeoReports\Api\Data\ProductReportInterface
     */
    public function setNameDuplicateCount($count)
    {
        return $this->setData(self::URL_PATH_LENGTH, $count);
    }

    /**
     * Set name length
     *
     * @param int $length
     * @return \MageWorx\SeoReports\Api\Data\ProductReportInterface
     */
    public function setNameLength($length)
    {
        return $this->setData(self::NAME_LENGTH, $length);
    }

    /**
     * Set meta title
     *
     * @param string $title
     * @return \MageWorx\SeoReports\Api\Data\ProductReportInterface
     */
    public function setMetaTitle($title)
    {
        return $this->setData(self::META_TITLE, $title);
    }

    /**
     * Set prepared meta title
     *
     * @return \MageWorx\SeoReports\Api\Data\ProductReportInterface
     */
    public function setPreparedMetaTitle($preparedTitle)
    {
        return $this->setData(self::PREPARED_META_TITLE, $preparedTitle);
    }

    /**
     * Set meta title length
     *
     * @param int $length
     * @return \MageWorx\SeoReports\Api\Data\ProductReportInterface
     */
    public function setMetaTitleLength($length)
    {
        return $this->setData(self::META_TITLE_LENGTH, $length);
    }

    /**
     * Set count of duplicated meta titles
     *
     * @type int $count
     * @return \MageWorx\SeoReports\Api\Data\ProductReportInterface
     */
    public function setMetaTitleDuplicateCount($count)
    {
        return $this->setData(self::META_TITLE_DUPLICATE_COUNT, $count);
    }

    /**
     * Set meta description length
     *
     * @param int $length
     * @return \MageWorx\SeoReports\Api\Data\ProductReportInterface
     */
    public function setMetaDescriptionLength($length)
    {
        return $this->setData(self::META_DESCRIPTION_LENGTH, $length);
    }

    /**
     * Set product URL
     *
     * @param string $url
     * @return \MageWorx\SeoReports\Api\Data\ProductReportInterface
     */
    public function setUrl($url)
    {
        return $this->setData(self::URL, $url);
    }

    /**
     * Set URL path length
     *
     * @type int $length
     * @return \MageWorx\SeoReports\Api\Data\ProductReportInterface
     */
    public function setUrlPathLength($length)
    {
        return $this->setData(self::URL_PATH_LENGTH, $length);
    }
}
