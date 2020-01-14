<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Api\Data;

/**
 * @api
 */
interface DpRedirectInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const REDIRECT_ID          = 'redirect_id';
    const PRODUCT_ID           = 'product_id';
    const PRODUCT_NAME         = 'product_name';
    const PRODUCT_SKU          = 'product_sku';
    const STORE_ID             = 'store_id';
    const REQUEST_PATH         = 'request_path';
    const CATEGORY_ID          = 'category_id';
    const PRIORITY_CATEGORY_ID = 'priority_category_id';
    const DATE_CREATED         = 'date_created';
    const HITS                 = 'hits';
    const STATUS               = 'status';

    /**
     * Get Redirect ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Deleted Product ID
     *
     * @return int|null
     */
    public function getProductId();

    /**
     * Get Deleted Product Name
     *
     * @return string
     */
    public function getProductName();

    /**
     * Get Deleted Product SKU
     *
     * @return string
     */
    public function getProductSku();

    /**
     * Get Redirect Store ID
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Get Request Path
     *
     * @return string
     */
    public function getRequestPath();

    /**
     * Get Category ID
     *
     * @return int
     */
    public function getCategoryId();

    /**
     * Get Priority Category ID
     *
     * @return int
     */
    public function getPriorityCategoryId();

    /**
     * Get Redirect Data Created
     *
     * @return string
     */
    public function getDateCreated();

    /**
     * Get Redirect Hits
     *
     * @return int
     */
    public function getHits();

    /**
     * Get Status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set Redirect ID
     *
     * @param int $id
     * @return DpRedirectInterface
     */
    public function setId($id);

    /**
     * Set Product ID
     *
     * @param int $productId
     * @return DpRedirectInterface
     */
    public function setProductId($productId);

    /**
     * Set Product Name
     *
     * @param string $productName
     * @return DpRedirectInterface
     */
    public function setProductName($productName);

    /**
     * Set Product SKU
     *
     * @param string $productSku
     * @return DpRedirectInterface
     */
    public function setProductSku($productSku);

    /**
     * Set Redirect Store ID
     *
     * @param int $storeId
     * @return DpRedirectInterface
     */
    public function setStoreId($storeId);

    /**
     * Set Redirect Request Path
     *
     * @param string $requestPath
     * @return DpRedirectInterface
     */
    public function setRequestPath($requestPath);

    /**
     * Set Category ID
     *
     * @param int $categoryId
     * @return DpRedirectInterface
     */
    public function setCategoryId($categoryId);

    /**
     * Set Priority Category ID
     *
     * @param int $categoryId
     * @return DpRedirectInterface
     */
    public function setPriorityCategoryId($categoryId);

    /**
     * Set Date Created
     *
     * @param string $dateCreated
     * @return DpRedirectInterface
     */
    public function setDataCreated($dateCreated);

    /**
     * Set Redirect Hits
     *
     * @param int $hits
     * @return DpRedirectInterface
     */
    public function setHits($hits);

    /**
     * Set Redirect Status
     *
     * @param int $statusCode
     * @return DpRedirectInterface
     */
    public function setStatus($statusCode);
}
