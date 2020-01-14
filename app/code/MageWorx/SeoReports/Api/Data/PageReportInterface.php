<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Api\Data;

/**
 * Page report interface.
 *
 * @api
 */
interface PageReportInterface extends \MageWorx\SeoReports\Api\ReportInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const REPORT_ID                  = 'entity_id';
    const REFERENCE_ID               = 'reference_id';
    const STORE_ID                   = 'store_id';
    const URL_PATH                   = 'url_path';
    const HEADING                    = 'heading';
    const PREPARED_HEADING           = 'prepared_heading';
    const HEADING_LENGTH             = 'heading_length';
    const HEADING_DUPLICATE_COUNT    = 'heading_duplicate_count';
    const META_TITLE                 = 'meta_title';
    const PREPARED_META_TITLE        = 'prepared_meta_title';
    const META_TITLE_LENGTH          = 'meta_title_length';
    const META_TITLE_DUPLICATE_COUNT = 'meta_title_duplicate_count';
    const META_DESCRIPTION_LENGTH    = 'meta_description_length';
    const URL_PATH_LENGTH            = 'url_path_length';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Page ID
     *
     * @return int
     */
    public function getRelationId();

    /**
     * Get store ID
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Get URL path
     *
     * @return string
     */
    public function getUrlPath();

    /**
     * Get heading
     *
     * @return string
     */
    public function getHeading();

    /**
     * Get prepared heading
     *
     * @return string
     */
    public function getPreparedHeading();

    /**
     * Get heading length
     *
     * @return string
     */
    public function getHeadingLength();

    /**
     * Get count of duplicated headings
     *
     * @return int
     */
    public function getHeadingDuplicateCount();

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
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setId($id);

    /**
     * Set relation ID
     *
     * @param int $relationId
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setRelationId($relationId);

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setStoreId($storeId);

    /**
     * Set URL path
     *
     * @param string $urlPath
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setUrlPath($urlPath);

    /**
     * Set heading
     *
     * @param string $heading
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setHeading($heading);

    /**
     * Set prepared heading
     *
     * @param string $preparedHeading
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setPreparedHeading($preparedHeading);

    /**
     * Set heading length
     *
     * @param int $length
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setHeadingLength($length);

    /**
     * Set count of duplicated headings
     *
     * @param int $count
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setHeadingDuplicateCount($count);

    /**
     * Set meta title
     *
     * @param string $title
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setMetaTitle($title);

    /**
     * Set prepared meta title
     *
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setPreparedMetaTitle($preparedTitle);

    /**
     * Set meta title length
     *
     * @param int $length
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setMetaTitleLength($length);

    /**
     * Set count of duplicated meta titles
     *
     * @type int $count
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setMetaTitleDuplicateCount($count);

    /**
     * Set meta description length
     *
     * @param int $length
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setMetaDescriptionLength($length);

    /**
     * Set count of duplicated URLs
     *
     * @type int $count
     * @return \MageWorx\SeoReports\Api\Data\PageReportInterface
     */
    public function setUrlPathLength($count);
}
