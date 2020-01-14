<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model\Generator;

use MageWorx\XmlSitemap\Helper\Data as Helper;
use Magento\Framework\ObjectManagerInterface;
use MageWorx\XmlSitemap\Model\ResourceModel\Catalog\CategoryFactory;

/**
 * {@inheritdoc}
 */
class Category extends AbstractGenerator
{
    /**
     * @var \Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * Category constructor.
     * @param Helper $helper
     * @param ObjectManagerInterface $objectManager
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Helper $helper,
        ObjectManagerInterface $objectManager,
        CategoryFactory $categoryFactory
    ) {
        $this->code = 'category';
        $this->name = __('Categories');
        $this->categoryFactory = $categoryFactory;
        parent::__construct($helper, $objectManager);
    }

    /**
     * @param $storeId
     * @param $writer
     * @param $counter
     */
    public function generate($storeId, $writer)
    {
        $this->storeId = $storeId;
        $this->helper->init($this->storeId);
        $this->storeBaseUrl = $writer->storeBaseUrl;

        $changefreq = $this->helper->getCategoryChangefreq($storeId);
        $priority   = $this->helper->getCategoryPriority($storeId);

        $altCodes   = $this->helper->getHreflangFinalCodes($this->code);

        $this->counter = 0;
        $categoryModel = $this->categoryFactory->create();
        while (!$categoryModel->isCollectionReaded()) {
            $collection = $categoryModel->getLimitedCollection($storeId, self::COLLECTION_LIMIT);
            $alternateUrlsCollection = $this->getAlternateUrlCollection($altCodes, $collection);

            foreach ($collection as $item) {
                $writer->write(
                    $this->getItemUrl($item),
                    $this->getItemChangeDate($item),
                    $changefreq,
                    $priority,
                    false,
                    $alternateUrlsCollection
                );
            }
            $this->counter += count($collection);
            unset($collection);
        }
    }

    /**
     * @param $altCodes
     * @param $collection
     * @return bool
     */
    protected function getAlternateUrlCollection($altCodes, $collection)
    {
        return false;
    }

    /**
     * @param $item
     * @return string
     */
    protected function getItemUrl($item)
    {
        $url = $this->storeBaseUrl . $item->getUrl();
        return $this->helper->trailingSlash($url);
    }
}