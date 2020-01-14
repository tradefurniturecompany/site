<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Model\ResourceModel\Template\CategoryFilter;

/**
 * Category Filter Collection
 */
class Collection extends \MageWorx\SeoXTemplates\Model\ResourceModel\Template\Category\Collection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'MageWorx\SeoXTemplates\Model\Template\CategoryFilter',
            'MageWorx\SeoXTemplates\Model\ResourceModel\Template\CategoryFilter'
        );
    }

    /**
     * Add attribute filter
     *
     * @param int $attributeId
     * @return
     */
    public function addAttributeFilter($attributeId)
    {
        $this->addFieldToFilter('attribute_id', $attributeId);
        return $this;
    }
}
