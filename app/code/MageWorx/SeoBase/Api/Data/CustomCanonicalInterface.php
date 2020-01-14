<?php
/**
 *
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Api\Data;

interface CustomCanonicalInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITY_ID          = 'entity_id';
    const SOURCE_ENTITY_TYPE = 'source_entity_type';
    const SOURCE_ENTITY_ID   = 'source_entity_id';
    const SOURCE_STORE_ID    = 'source_store_id';
    const TARGET_ENTITY_TYPE = 'target_entity_type';
    const TARGET_ENTITY_ID   = 'target_entity_id';
    const TARGET_STORE_ID    = 'target_store_id';

    /**
     * Get Entity Id
     *
     * @return int
     */
    public function getId();

    /**
     * Set Entity Id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get Source Entity Type
     *
     * @return string
     */
    public function getSourceEntityType();

    /**
     * Set Source Entity Type
     *
     * @param string $entityType
     * @return $this
     */
    public function setSourceEntityType($entityType);

    /**
     * Get Source Entity Id
     *
     * @return int
     */
    public function getSourceEntityId();

    /**
     * Set Source Entity Id
     *
     * @param int $entityId
     * @return $this
     */
    public function setSourceEntityId($entityId);

    /**
     * Get Source Store Id
     *
     * @return int
     */
    public function getSourceStoreId();

    /**
     * Set Source Store Id
     *
     * @param int $storeId
     * @return $this
     */
    public function setSourceStoreId($storeId);

    /**
     * Get Target Entity Type
     *
     * @return string
     */
    public function getTargetEntityType();

    /**
     * Set Target Entity Type
     *
     * @param string $entityType
     * @return $this
     */
    public function setTargetEntityType($entityType);

    /**
     * Get Target Entity Id
     *
     * @return string
     */
    public function getTargetEntityId();

    /**
     * Set Target Entity Id
     *
     * @param string $entityId
     * @return $this
     */
    public function setTargetEntityId($entityId);

    /**
     * Get Target Store Id
     *
     * @return int
     */
    public function getTargetStoreId();

    /**
     * Set Target Store Id
     *
     * @param int $storeId
     * @return $this
     */
    public function setTargetStoreId($storeId);
}
