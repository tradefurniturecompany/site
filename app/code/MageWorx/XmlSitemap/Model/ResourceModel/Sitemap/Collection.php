<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model\ResourceModel\Sitemap;

/**
 * {@inheritdoc}
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'sitemap_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\XmlSitemap\Model\Sitemap', 'MageWorx\XmlSitemap\Model\ResourceModel\Sitemap');
    }

    /**
     *
     * @param int|array $ids
     * @return $this
     */
    public function loadByIds($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        if (!empty($ids)) {
            $this->getSelect()->where('main_table.sitemap_id IN (?)', $ids);
        }
        return $this;
    }
}