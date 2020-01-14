<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoBase\Model\ResourceModel\Cms\Page;

use \MageWorx\SeoBase\Helper\Hreflangs as HelperHreflangs;
use Magento\Cms\Api\Data\PageInterface;
/**
 * SEO Base resource CMS page hreflang URLs
 */
class Hreflangs extends \MageWorx\SeoBase\Model\ResourceModel\Cms\Page
{
    /**
     * Retrieve array hreflang URLs:
     * [
     *      (int)itemId => [
     *          'identifier'   => (string)item URL identifier (URL key),
     *          'hreflangUrls' => [
     *              (int)storeId => (string)item store URL
     *          ]
     *       ]
     * ]
     *
     * @param array $storeIds
     * @param \Magento\Cms\Model\Page|int $page
     * @return array
     */
    public function getHreflangsData($storeIds, $page)
    {
        if (!is_object($page) && $page) {
            $page = $this->cmsFactory->create()->load($page);
            if (!$page || (is_object($page) && !$page->getPageId())) {
                return [];
            }
        }

        array_push($storeIds, 0);
        $pageId        = $page->getPageId();
        $pageHreflangs = [];

        $linkField = $this->linkFieldResolver->getLinkField(PageInterface::class, 'page_id');

        $select = $this->getConnection()->select()
            ->from(
                ['main_table' => $this->getMainTable()],
                [$this->getIdFieldName(), 'identifier', 'store_id' => "store_table.store_id"]
            )
            ->join(
                ['store_table' => $this->getTable('cms_page_store')],
                "main_table.$linkField = store_table.$linkField",
                []
            )
            ->where('main_table.is_active=1')
            ->where('store_table.store_id IN(?)', $storeIds);

        if ($pageId) {
            if ($this->helperData->getCmsPageRelationWay() == HelperHreflangs::CMS_RELATION_BY_ID) {
                $select->where('main_table.page_id = ' . $pageId);
            } elseif ($this->helperData->getCmsPageRelationWay() == HelperHreflangs::CMS_RELATION_BY_URLKEY) {
                $select->where("main_table.identifier = ?", $page->getIdentifier());
            } elseif ($this->helperData->getCmsPageRelationWay() == HelperHreflangs::CMS_RELATION_BY_IDENTIFIER) {
                $hreflangIdentifier = $page->getMageworxHreflangIdentifier();

                if (!$hreflangIdentifier) {
                    return [];
                }
                $select->where("main_table.mageworx_hreflang_identifier = ?", $hreflangIdentifier);
            }
        }

        $query  = $this->getConnection()->query($select);
        $queryResult  = $query->fetchAll();

        if (!$queryResult) {
            return [];
        }

        $sortResults   = $this->convertAndSortResult($queryResult, $pageId);
        $hreflangUrls  = [];

        foreach ($sortResults as $result) {
            if (!empty($result[0]) && $result[0]['store_id'] == 0) {
                foreach ($storeIds as $storeId) {
                    if ($storeId == 0) {
                        continue;
                    }

                    if (empty($hreflangUrls[$storeId])) {
                        $url = $this->helperStoreUrl->getUrl($result[0]['identifier'], $storeId, true);
                        if (!in_array($url, $hreflangUrls)) {
                            $hreflangUrls[$storeId] = $url;
                        }
                    }
                }
                $pageHreflangs[$pageId] = [
                    'identifier' => $result[0]['identifier'],
                    'hreflangUrls' => $hreflangUrls
                ];
                break;
            } else {
                foreach ($result as $row) {
                    $url = $this->helperStoreUrl->getUrl($row['identifier'], $row['store_id'], true);
                    if (!in_array($url, $hreflangUrls)) {
                        $hreflangUrls[$row['store_id']] = $url;
                    }
                }

                if (empty($row)) {
                    continue;
                }

                if ($this->helperData->getCmsPageRelationWay() == HelperHreflangs::CMS_RELATION_BY_URLKEY
                    || $this->helperData->getCmsPageRelationWay() == HelperHreflangs::CMS_RELATION_BY_IDENTIFIER
                ) {
                    if (!empty($pageHreflangs[$pageId]['identifier']['hreflangUrls'])) {
                        $pageHreflangs[$pageId]['hreflangUrls'] = $pageHreflangs[$pageId]['hreflangUrls'] + $hreflangUrls;
                    } else {
                        $pageHreflangs[$pageId] = [
                            'identifier' => $row['identifier'],
                            'hreflangUrls' => $hreflangUrls
                        ];
                    }
                } else {
                    $pageHreflangs[$row['page_id']] = [
                        'identifier' => $row['identifier'],
                        'hreflangUrls' => $hreflangUrls
                    ];
                }
            }
        }

        return $pageHreflangs;
    }

    /**
     *
     * @param array $queryResult
     * @param int $pageId
     * @return array
     */
    protected function convertAndSortResult($queryResult, $pageId)
    {
        $resultByPage = [];

        foreach ($queryResult as $row) {
            $resultByPage[$row['page_id']][$row['store_id']] = $row;
        }

        $partSortResult[$pageId] = $resultByPage[$pageId];
        unset($resultByPage[$pageId]);
        $sortResult = $partSortResult + $resultByPage;

        return $sortResult;
    }

    /**
     * Retrieve array hreflang URLs for home page:
     * [
     *      (int)itemId => [
     *          'identifier'   => (string)item URL identifier (URL key),
     *          'hreflangUrls' => [
     *              (int)storeId => (string)item store URL
     *          ]
     *       ]
     * ]
     *
     * @param array $storeIds
     * @param \Magento\Cms\Model\Page|int $page
     * @return array
     */
    public function getHreflangsDataForHomePage($storeIds, $page)
    {
        $hreflangUrls = [];
        if ($page && !is_object($page)) {
            $page = $this->cmsFactory->create()->load($page);
        }

        if (is_object($page)) {
            $pageId         = $page->getPageId();
            $pageIdentifier = $page->getIdentifier();
        } else {
            $pageId         = 0;
            $pageIdentifier = 'no-cms';
        }

        foreach ($storeIds as $storeId) {
            $url = $this->helperStoreUrl->getUrl('', $storeId, true, true);
            if (!in_array($url, $hreflangUrls)) {
                $hreflangUrls[$storeId] = $url;
            }
        }

        return [$pageId => ['identifier' => $pageIdentifier, 'hreflangUrls' => $hreflangUrls]];
    }
}
