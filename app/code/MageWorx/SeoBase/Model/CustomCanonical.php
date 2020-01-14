<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model;

use MageWorx\SeoBase\Api\Data\CustomCanonicalInterface;
use Magento\Framework\Model\AbstractModel;

class CustomCanonical extends AbstractModel implements CustomCanonicalInterface
{
    const CURRENT_CUSTOM_CANONICAL       = 'mageworx_seobase_customcanonical';
    const CUSTOM_CANONICAL_FORM_DATA_KEY = 'custom_canonical';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\SeoBase\Model\ResourceModel\CustomCanonical');
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(CustomCanonicalInterface::ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(CustomCanonicalInterface::ENTITY_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceEntityType()
    {
        return $this->getData(CustomCanonicalInterface::SOURCE_ENTITY_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceEntityType($entityType)
    {
        return $this->setData(CustomCanonicalInterface::SOURCE_ENTITY_TYPE, $entityType);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceEntityId()
    {
        return $this->getData(CustomCanonicalInterface::SOURCE_ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceEntityId($entityId)
    {
        return $this->setData(CustomCanonicalInterface::SOURCE_ENTITY_ID, $entityId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceStoreId()
    {
        return $this->getData(CustomCanonicalInterface::SOURCE_STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceStoreId($storeId)
    {
        return $this->setData(CustomCanonicalInterface::SOURCE_STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetEntityType()
    {
        return $this->getData(CustomCanonicalInterface::TARGET_ENTITY_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetEntityType($entityType)
    {
        return $this->setData(CustomCanonicalInterface::TARGET_ENTITY_TYPE, $entityType);
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetEntityId()
    {
        return $this->getData(CustomCanonicalInterface::TARGET_ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetEntityId($entityId)
    {
        return $this->setData(CustomCanonicalInterface::TARGET_ENTITY_ID, $entityId);
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetStoreId()
    {
        return $this->getData(CustomCanonicalInterface::TARGET_STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetStoreId($storeId)
    {
        return $this->setData(CustomCanonicalInterface::TARGET_STORE_ID, $storeId);
    }
}
