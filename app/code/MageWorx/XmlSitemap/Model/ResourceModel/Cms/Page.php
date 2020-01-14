<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model\ResourceModel\Cms;

use Magento\Cms\Api\Data\PageInterface;

/**
 * {@inheritdoc}
 */
class Page extends \Magento\Sitemap\Model\ResourceModel\Cms\Page
{
    /**
     * Retrieve cms page collection array
     *
     * @param int $storeId
     * @return array
     */
    public function getCollection($storeId)
    {
        $entityMetadata = $this->metadataPool->getMetadata(PageInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $select = $this->getConnection()->select()->from(
            ['main_table' => $this->getMainTable()],
            [$this->getIdFieldName(), 'url' => 'identifier', 'updated_at' => 'update_time']
        )->join(
            ['store_table' => $this->getTable('cms_page_store')],
            "main_table.{$linkField} = store_table.$linkField",
            []
        )->where(
            'main_table.is_active = 1'
        )->where(
            'main_table.in_xml_sitemap = 1'
        )->where(
            'main_table.identifier != ?',
            \Magento\Cms\Model\Page::NOROUTE_PAGE_ID
        )->where(
            'store_table.store_id IN(?)',
            [0, $storeId]
        );

        $cmsPages = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $page = $this->_prepareObject($row);
            $cmsPages[$page->getId()] = $page;
        }

        return $cmsPages;
    }

}
