<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Api\Data;

/**
 * Category report interface.
 *
 * @api
 */
interface CategoryReportInterface extends \MageWorx\SeoReports\Api\ReportInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const REPORT_ID                  = 'entity_id';
    const REFERENCE_ID               = 'reference_id';
    const STORE_ID                   = 'store_id';
    const LEVEL                      = 'level';
    const URL_PATH                   = 'url_path';
    const URL_PATH_LENGTH            = 'url_path_length';
    const NAME                       = 'name';
    const NAME_LENGTH                = 'name_length';
    const PREPARED_NAME              = 'prepared_name';
    const NAME_DUPLICATE_COUNT       = 'name_duplicate_count';
    const META_TITLE                 = 'meta_title';
    const PREPARED_META_TITLE        = 'prepared_meta_title';
    const META_TITLE_LENGTH          = 'meta_title_length';
    const META_TITLE_DUPLICATE_COUNT = 'meta_title_duplicate_count';
    const META_DESCRIPTION_LENGTH    = 'meta_description_length';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get reference(category) ID
     *
     * @return int
     */
    public function getReferenceId();

    /**
     * Get store ID
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Get level
     *
     * @return int
     */
    public function getLevel();

    /**
     * Get URL path
     *
     * @return string
     */
    public function getUrlPath();

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get prepared name
     *
     * @return string
     */
    public function getPreparedName();

    /**
     * Get count of duplicated names
     *
     * @return int
     */
    public function getNameDuplicateCount();

    /**
     * Get name length
     *
     * @return int
     */
    public function getNameLength();

    /**
     * Get meta title
     *
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * Get prepared meta title
     *
     * @return string|null
     */
    public function getPreparedMetaTitle();

    /**
     * Get meta title length
     *
     * @return int
     */
    public function getMetaTitleLength();

    /**
     * Get count of duplicated meta titles
     *
     * @return int
     */
    public function getMetaTitleDuplicateCount();

    /**
     * Get meta description length
     *
     * @return int
     */
    public function getMetaDescriptionLength();

    /**
     * Get count of duplicated URLs
     *
     * @return int
     */
    public function getUrlPathLength();

    /**
     * Set ID
     *
     * @param int $id
     * @return \MageWorx\SeoReports\Api\Data\CategoryReportInterface
     */
    public function setId($id);

    /**
     * Set reference(category) ID
     *
     * @param int $referenceId
     * @return \MageWorx\SeoReports\Api\Data\CategoryReportInterface
     */
    public function setReferenceId($referenceId);

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return \MageWorx\SeoReports\Api\Data\CategoryReportInterface
     */
    public function setStoreId($storeId);

    /**
     * Set level
     *
     * @param int $level
     * @return \MageWorx\SeoReports\Api\Data\CategoryReportInterface
     */
    public function setLevel($level);

    /**
     * Set URL path
     *
     * @param string $urlPath
     * @return \MageWorx\SeoReports\Api\Data\CategoryReportInterface
     */
    public function setUrlPath($urlPath);

    /**
     * Set name
     *
     * @param string $name
     * @return \MageWorx\SeoReports\Api\Data\CategoryReportInterface
     */
    public function setName($name);

    /**
     * Set prepared name
     *
     * @param string $preparedName
     * @return \MageWorx\SeoReports\Api\Data\CategoryReportInterface
     */
    public function setPreparedName($preparedName);

    /**
     * Set count of duplicated names
     *
     * @param int $count
     * @return \MageWorx\SeoReports\Api\Data\CategoryReportInterface
     */
    public function setNameDuplicateCount($count);

    /**
     * Set name length
     *
     * @param int $length
     * @return \MageWorx\SeoReports\Api\Data\CategoryReportInterface
     */
    public function setNameLength($length);

    /**
     * Set meta title
     *
     * @param string $title
     * @return \MageWorx\SeoReports\Api\Data\CategoryReportInterface
     */
    public function setMetaTitle($title);

    /**
     * Set prepared meta title
     *
     * @return \MageWorx\SeoReports\Api\Data\CategoryReportInterface
     */
    public function setPreparedMetaTitle($preparedTitle);

    /**
     * Set meta title length
     *
     * @param int $length
     * @return \MageWorx\SeoReports\Api\Data\CategoryReportInterface
     */
    public function setMetaTitleLength($length);

    /**
     * Set count of duplicated meta titles
     *
     * @type int $count
     * @return \MageWorx\SeoReports\Api\Data\CategoryReportInterface
     */
    public function setMetaTitleDuplicateCount($count);

    /**
     * Set meta description length
     *
     * @param int $length
     * @return \MageWorx\SeoReports\Api\Data\CategoryReportInterface
     */
    public function setMetaDescriptionLength($length);

    /**
     * Set count of duplicated URLs
     *
     * @type int $count
     * @return \MageWorx\SeoReports\Api\Data\CategoryReportInterface
     */
    public function setUrlPathLength($count);
}
