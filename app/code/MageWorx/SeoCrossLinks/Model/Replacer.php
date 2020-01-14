<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Model;

use MageWorx\SeoCrossLinks\Model\ResourceModel\Catalog\ProductFactory;
use MageWorx\SeoCrossLinks\Model\ResourceModel\Catalog\CategoryFactory;
use MageWorx\SeoCrossLinks\Helper\Data as HelperData;
use MageWorx\SeoCrossLinks\Helper\StoreUrl as HelperStoreUrl;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;

class Replacer
{
    /**
     * @var \MageWorx\SeoCrossLinks\Model\ResourceModel\Catalog\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \MageWorx\SeoCrossLinks\Model\ResourceModel\Catalog\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \MageWorx\SeoCrossLinks\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\SeoCrossLinks\Helper\StoreUrl
     */
    protected $helperStoreUrl;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @var array
     */
    protected $productDataList;

    /**
     *
     * @var array
     */
    protected $categoryDataList;

    /**
     *
     * @var array
     */
    protected $landingpageDataList;

    /** @var EventManagerInterface */
    protected $eventManager;

    /**
     * The replaced pairs before converting.
     * The order is important.
     *
     * @var array
     */
    protected $exceptFromConvert = array(
            '&amp;'  => '!$#amp#$!',
            '& '      => '!$#a#$!'
        );

    /**
     * Replacer constructor.
     *
     * @param EventManagerInterface $eventManager
     * @param ProductFactory $productFactory
     * @param CategoryFactory $categoryFactory
     * @param HelperData $helperData
     * @param HelperStoreUrl $helperStoreUrl
     * @param UrlInterface $url
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        EventManagerInterface $eventManager,
        ProductFactory $productFactory,
        CategoryFactory $categoryFactory,
        HelperData $helperData,
        HelperStoreUrl $helperStoreUrl,
        UrlInterface $url,
        StoreManagerInterface $storeManager
    ) {
        $this->eventManager    = $eventManager;
        $this->productFactory  = $productFactory;
        $this->categoryFactory = $categoryFactory;
        $this->helperData      = $helperData;
        $this->helperStoreUrl  = $helperStoreUrl;
        $this->url             = $url;
        $this->storeManager    = $storeManager;
    }

    /**
     * Replace keywords to links in html
     *
     * @param $collection
     * @param $html
     * @param $maxReplaceCount
     * @param null $ignoreProductSku
     * @param null $ignoreCategoryId
     * @param null $ignoreLandingPageId
     * @return bool|string
     */
    public function replace(
        $collection,
        $html,
        $maxReplaceCount,
        $ignoreProductSku = null,
        $ignoreCategoryId = null,
        $ignoreLandingPageId = null
    ) {
        if (!trim($html)) {
            return false;
        }
        $preparedHtml = $this->_prepareBeforeConvert($html);

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;

        libxml_use_internal_errors(true);
        $dom->loadHTML($preparedHtml);
        libxml_clear_errors();

        $textParts   = array();
        $xpath       = new \DOMXPath($dom);
        $domNodeList = $xpath->query('//text()[not(ancestor::script)][not(ancestor::a)]');

        foreach ($domNodeList as $node) {
            if ($node->nodeType === 3) {
                $textParts[] = $node->wholeText;
            }
        }

        if (!count($collection->getItems())) {
            return false;
        }

        $this->_specifyCollection($collection, $textParts, $maxReplaceCount);

        $pairs = array();
        $textPartsMod = $this->_dispatchByDestination(
            $collection,
            $textParts,
            $maxReplaceCount,
            $ignoreProductSku,
            $ignoreCategoryId,
            $ignoreLandingPageId,
            $pairs
        );

        foreach ($domNodeList as $node) {
            if ($node->nodeType !== 3) {
                continue;
            }

            $replace = array_shift($textPartsMod);
            $newNode = $dom->createDocumentFragment();
            $newNode->appendXML($replace);
            if (is_object($node->parentNode)) {
                $node->parentNode->replaceChild($newNode, $node);
            }
        }
        $convertedHtml = $dom->saveHTML();

        if (!$convertedHtml) {
            return false;
        }

        $modifyHtml = str_replace(array_keys($pairs), array_values($pairs), $convertedHtml);

        return $this->_recoveryAfterConvert($modifyHtml);
    }

    /**
     *
     * @param string $html
     * @return string
     */
    protected function _cropWrapper($html)
    {
        $posBodyStart = mb_strpos($html, '<body>');
        $posBodyEnd   = mb_strpos($html, '</body>');

        if ($posBodyEnd !== false) {
            $html = mb_substr($html, 0, $posBodyEnd);
        }

        if ($posBodyStart !== false) {
            $html = mb_substr($html, $posBodyStart + 6);
        }

        return $html;
    }

    /**
     * Replaces certain characters
     *
     * @param type $html
     * @return type
     */
    protected function _prepareBeforeConvert($html)
    {
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
        return str_replace(array_keys($this->exceptFromConvert), array_values($this->exceptFromConvert), $html);
    }

    /**
     * Recovers the characters replaced earlier
     *
     * @param string $html
     * @return string
     */
    protected function _recoveryAfterConvert($html)
    {
        $croppedHtml = $this->_cropWrapper($html);
        return str_replace(array_values($this->exceptFromConvert), array_keys($this->exceptFromConvert), $croppedHtml);
    }

    /**
     * Delegate replacements if URL exists
     *
     * @param \MageWorx\SeoCrossLinks\Model\ResourceModel\Crosslink\Collection $collection
     * @param array $textParts
     * @param int $maxGlobalCount
     * @param string $ignoreProductSku
     * @param int $ignoreCategoryId
     * @param array $pairs
     * @return array
     */
    protected function _dispatchByDestination(
        $collection,
        $textParts,
        $maxGlobalCount,
        $ignoreProductSku,
        $ignoreCategoryId,
        $ignoreLandingPageId,
        &$pairs
    ) {

        foreach ($collection as $crosslink) {
            if (!$maxGlobalCount) {
                continue;
            }
            if ($crosslink->getRefProductSku()) {
                $productUrlData = $this->_getProductData($collection, $crosslink, $ignoreProductSku);

                if ($productUrlData) {
                    $textParts = $this->_preliminaryReplaceAndCreateReplacementPairs(
                        $textParts,
                        $crosslink,
                        $productUrlData['url'],
                        $productUrlData['name'],
                        $maxGlobalCount,
                        $pairs
                    );
                }
            } elseif ($crosslink->getRefCategoryId()) {
                $categoryUrlData = $this->_getCategoryData($collection, $crosslink, $ignoreCategoryId);
                if ($categoryUrlData) {
                    $textParts = $this->_preliminaryReplaceAndCreateReplacementPairs(
                        $textParts,
                        $crosslink,
                        $categoryUrlData['url'],
                        $categoryUrlData['name'],
                        $maxGlobalCount,
                        $pairs
                    );
                }
            } elseif ($crosslink->getRefStaticUrl()) {
                $staticUrl = $this->_getStaticUrl($crosslink);

                if ($staticUrl) {
                    $textParts = $this->_preliminaryReplaceAndCreateReplacementPairs(
                        $textParts,
                        $crosslink,
                        $staticUrl,
                        false,
                        $maxGlobalCount,
                        $pairs
                    );
                }
            } elseif ($crosslink->getRefLandingpageId()) {
                $landingPageData = $this->_getLandingpageData($collection, $crosslink, $ignoreLandingPageId);

                if ($landingPageData) {
                    $textParts = $this->_preliminaryReplaceAndCreateReplacementPairs(
                        $textParts,
                        $crosslink,
                        $landingPageData['url'],
                        $landingPageData['header'],
                        $maxGlobalCount,
                        $pairs
                    );
                }
            }
        }

        return $textParts;
    }

    /**
     * Retrive list of modified text parts ( ...keyword... => ...hash... )
     * Fill $pairs array (hash => URL)
     *
     *
     * @param array $textParts
     * @param \MageWorx\SeoCrossLinks\Model\Crosslink $crosslink
     * @param string $url
     * @param int $maxGlobalCount
     * @param array $pairs
     * @return array
     */
    protected function _preliminaryReplaceAndCreateReplacementPairs($textParts, $crosslink, $url, $name, &$maxGlobalCount, &$pairs)
    {
        $replaceCount = 0;
        if ($crosslink->getKeywords()) {
            foreach ($crosslink->getKeywords() as $keyword) {
                $availableCount = 1;

                if ($maxGlobalCount == 0) {
                    continue ;
                }

                $pattern        = $this->_getReplacePattern($keyword);
                $href           = $this->_getHtmlHref($crosslink, $keyword, $url, $name);

                for ($i= 0; $i < count($textParts); $i++) {
                    if ($maxGlobalCount == 0) {
                        break 2;
                    }

                    $key = substr(md5(rand()), 0, 7);
                    $res = preg_replace($pattern, $key, $textParts[$i], $availableCount, $replaceCount);

                    if ($res && $replaceCount) {
                        $pairs[$key] = $href;
                        $availableCount -= $replaceCount;
                        $maxGlobalCount -= $replaceCount;
                        $textParts[$i] = $res;
                        break;
                    }
                }
            }
        } else {
            $keyword        = $crosslink->getKeyword();
            $availableCount = min(array($maxGlobalCount, $crosslink->getReplacementCount()));
            $pattern        = $this->_getReplacePattern($keyword);
            $href           = $this->_getHtmlHref($crosslink, $keyword, $url, $name);

            for ($i= 0; $i < count($textParts); $i++) {
                $key = substr(md5(rand()), 0, 7);
                $res = preg_replace($pattern, $key, $textParts[$i], $availableCount, $replaceCount);

                if ($res && $replaceCount) {
                    $pairs[$key] = $href;
                    $availableCount -= $replaceCount;
                    $maxGlobalCount -= $replaceCount;
                    $textParts[$i] = $res;
                }
            }
        }

        return $textParts;
    }

    /**
     * Retrive product data (URL, name) if it is not current URL/product
     *
     * @param \MageWorx\SeoCrossLinks\Model\ResourceModel\Crosslink\Collection $collection
     * @param \MageWorx\SeoCrossLinks\Model\Crosslink $crosslink
     * @param string $ignoreProductSku
     * @return string
     */
    protected function _getProductData($collection, $crosslink, $ignoreProductSku)
    {
        if (is_null($this->productDataList)) {
            $prodSkuList = array();
            foreach ($collection as $item) {
                if ($item->getRefProductSku() && $item->getRefProductSku() != $ignoreProductSku) {
                    $prodSkuList[] = $item->getRefProductSku();
                }
            }

            $store     = $this->storeManager->getStore();
            $isUseName = ($this->helperData->isUseNameForTitle() !=
               \MageWorx\SeoCrossLinks\Model\CrossLink::USE_CROSSLINK_TITLE_ONLY
            );

            $this->productDataList = $this->productFactory->create()->getCollection($prodSkuList, $store, $isUseName);
        }

        if (!empty($this->productDataList[$crosslink->getRefProductSku()]['url']) &&
            !$this->_isCurrentUrl($this->productDataList[$crosslink->getRefProductSku()]['url'])
        ) {
            return $this->productDataList[$crosslink->getRefProductSku()];
        }

        return false;
    }

    /**
     * Retrive category data (URL, name) if it is not current URL/category
     *
     * @param \MageWorx\SeoCrossLinks\Model\ResourceModel\Crosslink\Collection $collection
     * @param \MageWorx\SeoCrossLinks\Model\Crosslink $crosslink
     * @param int $ignoreCategoryId
     * @return string
     */
    protected function _getCategoryData($collection, $crosslink, $ignoreCategoryId)
    {
        if (is_null($this->categoryDataList)) {
            $catIds = array();
            foreach ($collection as $item) {
                if ($item->getRefCategoryId() && $item->getRefCategoryId() != $ignoreCategoryId) {
                    $catIds[] = $item->getRefCategoryId();
                }
            }

            $store     = $this->storeManager->getStore();
            $isUseName = ($this->helperData->isUseNameForTitle() !=
               \MageWorx\SeoCrossLinks\Model\CrossLink::USE_CROSSLINK_TITLE_ONLY
            );
            $this->categoryDataList = $this->categoryFactory->create()->getCollection($catIds, $store, $isUseName);
        }

        if (!empty($this->categoryDataList[$crosslink->getRefCategoryId()]['url']) &&
            !$this->_isCurrentUrl($this->categoryDataList[$crosslink->getRefCategoryId()]['url'])
        ) {
            return $this->categoryDataList[$crosslink->getRefCategoryId()];
        }

        return false;
    }

    /**
     *  Retrive landing page data (URL, name) if it is not current landing page
     *
     * @param $collection
     * @param $crosslink
     * @param $ignoreLandingPageId
     * @return bool|mixed
     */
    protected function _getLandingpageData($collection, $crosslink, $ignoreLandingPageId)
    {
        if (is_null($this->landingpageDataList)) {
            $lpIds = array();
            foreach ($collection as $item) {
                if ($item->getRefLandingpageId() && $item->getRefLandingpageId() != $ignoreLandingPageId) {
                    $lpIds[] = $item->getRefLandingpageId();
                }
            }

            $data = new DataObject();
            $data->setIds($lpIds);
            $data->setLandingpagesData([]);
            $this->eventManager->dispatch(
                'mageworx_landingpages_get_landingpages_data',
                ['object' => $data]
            );

            $this->landingpageDataList = $data->getLandingpagesData();
        }

        if (!empty($this->landingpageDataList[$crosslink->getRefLandingpageId()]['url']) &&
            !$this->_isCurrentUrl($this->landingpageDataList[$crosslink->getRefLandingpageId()]['url'])
        ) {
            return $this->landingpageDataList[$crosslink->getRefLandingpageId()];
        }

        return false;
    }

    /**
     * Retrive URL
     *
     * @param \MageWorx\SeoCrossLinks\Model\Crosslink $crosslink
     * @return string
     */
    protected function _getStaticUrl($crosslink)
    {
        if (strpos($crosslink->getRefStaticUrl(), '://') === false) {
            $staticUrl = $this->helperStoreUrl->getUrl($crosslink->getRefStaticUrl());
        } else {
            $staticUrl = trim($crosslink->getRefStaticUrl());
        }

        if (!$this->_isCurrentUrl($staticUrl)) {
            return $staticUrl;
        }
        return false;
    }

    /**
     * Minimize collection using search keywords in text and keyword count
     *
     * @param MageWorx_SeoCrossLinks_Model_Resource_Crosslink_Collection $collection
     * @param array $textParts
     * @param int $maxReplaceAllCount
     */
    protected function _specifyCollection($collection, $textParts, $maxReplaceAllCount)
    {
        $text = implode(' ***mageworx*** ', $textParts);
        $replaceStaticUrlCount = 0;

        foreach ($collection->getItems() as $item) {
            if ($replaceStaticUrlCount > $maxReplaceAllCount) {
                $collection->removeItemByKey($item->getCrosslinkId());
                continue;
            }

            $replaceCount = $this->_renderCrossLink($item, $text);
            if ($item->getRefStaticUrl()) {
                $replaceStaticUrlCount += $replaceCount;
            }
        }
    }

    /**
     * Return count of matches or false.
     * Rewrite keyword value for crosslink:
     * if identical match found modify crosslink keyword "cak+" => cake
     * else create keywords property in crosslink model
     *
     * @param \MageWorx\SeoCrossLinks\Model\Crosslink $crosslink
     * @param string $text
     * @return int|false
     */
    protected function _renderCrossLink($crosslink, $text)
    {
        if (stripos($text, trim($crosslink->getKeyword(), '+')) !== false) {
            $pattern = $this->_getMatchPattern($crosslink->getKeyword());
            $matches = array();

            $res = preg_match_all($pattern, $text, $matches);
            if ($res) {
                $cropMatches = array_slice($matches[0], 0, $crosslink->getReplacementCount());

                if (count($cropMatches) == 1) {
                    $crosslink->setKeyword($cropMatches[0]);
                } else {
                    $crosslink->setKeywords($cropMatches);
                }
                return (count($cropMatches));
            }
        }
        return false;
    }

    /**
     * Convert string to regexp
     *
     * @param string $keyword
     * @return string
     */
    protected function _getMatchPattern($keyword)
    {
        $keyword = trim($keyword);
        if (substr($keyword, 0, 1) == '+') {
            $leftPlus = true;
            $keyword  = ltrim($keyword, '+');
        }
        if (substr($keyword, -1, 1) == '+') {
            $rightPlus = true;
            $keyword   = rtrim($keyword, '+');
        }

        $keyword = preg_quote($keyword, '/');

        if (!empty($leftPlus)) {
            $keyword = '[^\s.<,]*' . $keyword;
        } else {
            $keyword = '(\b)' . $keyword;
        }

        if (!empty($rightPlus)) {
            $keyword = rtrim($keyword, '+') . '[^\s.<,]*';
        } else {
            $keyword = $keyword . '(\b)';
        }

        return '/' . $keyword . '/iu';
    }

    /**
     * Convert string to regexp
     *
     * @param string $keyword
     * @return string
     */
    protected function _getReplacePattern($keyword)
    {
        return '/(\b)' . preg_quote($keyword, '/') . '(\b)/iu';
    }

    /**
     * Retrive HTML link
     *
     * @param \MageWorx\SeoCrossLinks\Model\Crosslink $crosslink
     * @param string $keyword
     * @param string $urlRaw
     * @return string
     */
    protected function _getHtmlHref($crosslink, $keywordRaw, $urlRaw, $name)
    {
        $url     = htmlspecialchars($urlRaw, ENT_COMPAT, 'UTF-8', false);
        $target  = $crosslink->getTargetLinkValue($crosslink->getLinkTarget());

        switch ($this->helperData->isUseNameForTitle()) {
            case Crosslink::USE_CROSSLINK_TITLE_ONLY:
                $title = $crosslink->getLinkTitle();
                break;
            case Crosslink::USE_NAME_IF_EMPTY_TITLE:
                $title = trim($crosslink->getLinkTitle()) ? $crosslink->getLinkTitle() : $name;
                break;
            case Crosslink::USE_NAME_ALWAYS:
                $title = $name;
                break;
        }

        $title   = htmlspecialchars(strip_tags($title));
        $keyword = htmlspecialchars($keywordRaw);
        $class   = $this->helperData->getLinkClass();

        $crosslinkHtml = "<a " . $class . " href=\"" . $url . "\" target=\"" . $target . "\" alt=\"" . $title . "\" title=\"" . $title . "\"";
        if ($crosslink->getNofollowRel()) {
            $crosslinkHtml .= ' rel="nofollow"';
        }
        return   $crosslinkHtml . ">" . $keyword . "</a>";
    }

    /**
     * Check if input string is current URL
     *
     * @param string $url
     * @return bool
     */
    protected function _isCurrentUrl($url)
    {
        list($currentUrl) = explode('?', $this->url->getCurrentUrl());

        return (mb_substr($currentUrl, mb_strpos($currentUrl, '://')) == mb_substr($url, mb_strpos($url, '://')));
    }
}
