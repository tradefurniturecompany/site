<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * SEO Base Custom Canonical URL Helper
 */

namespace MageWorx\SeoBase\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;
use MageWorx\SeoBase\Api\Data\CustomCanonicalInterface;
use MageWorx\SeoBase\Model\CustomCanonical as CustomCanonicalModel;
use MageWorx\SeoBase\Model\Source\CustomCanonical\TargetStoreId;
use MageWorx\SeoBase\Model\Source\CustomCanonical\SourceTypeEntity as SourceTypeEntityOptions;
use MageWorx\SeoBase\Model\Source\CustomCanonical\TargetTypeEntity as TargetTypeEntityOptions;

class CustomCanonical extends AbstractHelper
{
    const PRODUCT_CHOOSER_VALUE_PREFIX  = 'product/';
    const CATEGORY_CHOOSER_VALUE_PREFIX = 'category/';

    /**
     * @var SourceTypeEntityOptions
     */
    private $sourceTypeEntityOptions;

    /**
     * @var TargetTypeEntityOptions
     */
    private $targetTypeEntityOptions;

    /**
     * CustomCanonical constructor.
     *
     * @param Context $context
     * @param SourceTypeEntityOptions $sourceTypeEntityOptions
     * @param TargetTypeEntityOptions $targetTypeEntityOptions
     */
    public function __construct(
        Context $context,
        SourceTypeEntityOptions $sourceTypeEntityOptions,
        TargetTypeEntityOptions $targetTypeEntityOptions
    ) {
        $this->sourceTypeEntityOptions = $sourceTypeEntityOptions;
        $this->targetTypeEntityOptions = $targetTypeEntityOptions;
        parent::__construct($context);
    }

    /**
     * @param string $nameSuffix
     * @return array
     */
    public function getSourceChooserTypeOptions($nameSuffix = CustomCanonicalInterface::SOURCE_ENTITY_ID)
    {
        return $this->getChooserTypeOptions($this->sourceTypeEntityOptions, $nameSuffix);
    }

    /**
     * @param string $nameSuffix
     * @return array
     */
    public function getTargetChooserTypeOptions($nameSuffix = CustomCanonicalInterface::TARGET_ENTITY_ID)
    {
        return $this->getChooserTypeOptions($this->targetTypeEntityOptions, $nameSuffix);
    }

    /**
     * @param string $EntityIdValue
     * @param string $entityType
     * @return string
     */
    public function cropChooserValuePrefix($EntityIdValue, $entityType)
    {
        if ($entityType == Rewrite::ENTITY_TYPE_PRODUCT) {
            return str_replace(self::PRODUCT_CHOOSER_VALUE_PREFIX, '', $EntityIdValue);
        }

        if ($entityType == Rewrite::ENTITY_TYPE_CATEGORY) {
            return str_replace(self::CATEGORY_CHOOSER_VALUE_PREFIX, '', $EntityIdValue);
        }

        return $EntityIdValue;
    }

    /**
     * @param CustomCanonicalModel $customCanonical
     * @return bool
     */
    public function isRecursiveCustomCanonical($customCanonical)
    {
        $data          = $customCanonical->getData();
        $sourceStoreId = $data[CustomCanonicalModel::SOURCE_STORE_ID];
        $targetStoreId = $data[CustomCanonicalModel::TARGET_STORE_ID];

        if ($sourceStoreId == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
            return false;
        }

        if ($targetStoreId == TargetStoreId::SAME_AS_SOURCE_ENTITY) {
            $targetStoreId = $sourceStoreId;
        }

        return $data[CustomCanonicalModel::SOURCE_ENTITY_TYPE] == $data[CustomCanonicalModel::TARGET_ENTITY_TYPE]
            && $data[CustomCanonicalModel::SOURCE_ENTITY_ID] == $data[CustomCanonicalModel::TARGET_ENTITY_ID]
            && $sourceStoreId == $targetStoreId;
    }

    /**
     * Get options in "key-value" format
     *
     * @param \Magento\Framework\Data\OptionSourceInterface $entityTypeOptions
     * @param string $nameSuffix
     * @return array
     */
    private function getChooserTypeOptions($entityTypeOptions, $nameSuffix)
    {
        $tmpOptions = $entityTypeOptions->toOptionArray();
        $options    = [];

        foreach ($tmpOptions as $option) {

            if ($option['value'] == Rewrite::ENTITY_TYPE_CUSTOM) {
                continue;
            } elseif ($option['value'] == Rewrite::ENTITY_TYPE_CMS_PAGE) {
                $options[$option['value']] = str_replace('-', '_', $option['value']) . '_' . $nameSuffix;
            } else {
                $options[$option['value']] = $option['value'] . '_' . $nameSuffix;
            }
        }

        return $options;
    }
}
