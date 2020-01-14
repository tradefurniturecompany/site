<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Api\Data;

/**
 * SEO Category Filter interface
 * @api
 */
interface CategoryFilterInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const CATEGORY_FILTER_ID                 = 'id';
    const CATEGORY_FILTER_CATEGORY_ID        = 'category_id';
    const CATEGORY_FILTER_ATTRIBUTE_ID       = 'attribute_id';
    const CATEGORY_FILTER_STORE_ID           = 'store_id';
    const CATEGORY_FILTER_META_TITLE         = 'meta_title';
    const CATEGORY_FILTER_META_DESCRIPTION   = 'meta_description';
    const CATEGORY_FILTER_DESCRIPTION        = 'description';
    const CATEGORY_FILTER_META_KEYWORDS      = 'meta_keywords';
    /**#@-*/

    /**
     * @since 2.5.0
     */
    const CATEGORY_FILTER_ATTRIBUTE_OPTION_ID = 'attribute_option_id';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get category ID
     *
     * @return int
     */
    public function getCategoryId();

    /**
     * Get attribute ID
     *
     * @return int
     */
    public function getAttributeId();

    /**
     * Get attribute option ID
     *
     * @since 2.5.0
     * @return int
     */
    public function getAttributeOptionId();

    /**
     * Get store ID
     *
     * @return int|null
     */
    public function getStoreId();

    /**
     * Get meta title
     *
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * Get meta description
     *
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Get meta keywords
     *
     * @return string|null
     */
    public function getMetaKeywords();

    /**
     * Set ID
     *
     * @param int $id
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setId($id);

    /**
     * Set category ID
     *
     * @param int $categoryId
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setCategoryId($categoryId);

    /**
     * Set attribute ID
     *
     * @param int $attributeId
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setAttributeId($attributeId);

    /**
     * Set attribute option ID
     *
     * @since 2.5.0
     * @param int $attributeId
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setAttributeOptionId($attributeOptionId);

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setStoreId($storeId);

    /**
     * Set meta title
     *
     * @param string|null $metaTitle
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setMetaTitle($metaTitle);

    /**
     * Set meta description
     *
     * @param string|null $metaDescription
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setMetaDescription($metaDescription);

    /**
     * Set description
     *
     * @param string|null $description
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setDescription($description);

    /**
     * Set meta keywords
     *
     * @param string|null $metaKeywords
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setMetaLeywords($metaKeywords);
}
