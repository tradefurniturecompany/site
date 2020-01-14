<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Plugin;

use MageWorx\SeoMarkup\Helper\Category as HelperData;
use MageWorx\SeoMarkup\Helper\LandingPage as HelperDataLandingPage;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Layout;
use MageWorx\SeoMarkup\Helper\Json\Category as HelperJsonCategory;
use MageWorx\SeoMarkup\Helper\Json\LandingPage as HelperJsonLandingPage;

/**
 * Magento doesn't add scope ([GLOBAL], [STORE_VIEW], etc) for custom category attributes.
 *
 * @see https://github.com/magento/magento2/issues/13440
 *
 */
class AddCategoryCustomAttributesPlugin extends \Magento\Catalog\Model\Category\DataProvider
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var array
     */
    protected $data;

    /**
     * AddCategoryCustomAttributesPlugin constructor.
     *
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        $data = []
    ) {
        $this->eavConfig = $eavConfig;
        $this->data      = $data;
    }

    /**
     * @param \Magento\Catalog\Model\Category\DataProvider $subject
     * @param array $result
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterPrepareMeta(\Magento\Catalog\Model\Category\DataProvider $subject, $result)
    {
        $meta = array_replace_recursive(
            $result,
            $this->_prepareFieldsMeta(
                $this->_getFieldsMap(),
                $subject->getAttributesMeta($this->eavConfig->getEntityType('catalog_category'))
            )
        );

        return $meta;
    }

    /**
     * @param $fieldsMap
     * @param $fieldsMeta
     * @return array
     */
    protected function _prepareFieldsMeta($fieldsMap, $fieldsMeta)
    {
        $result = [];
        foreach ($fieldsMap as $fieldSet => $fields) {
            foreach ($fields as $field) {
                if (isset($fieldsMeta[$field])) {
                    $result[$fieldSet]['children'][$field]['arguments']['data']['config'] = $fieldsMeta[$field];
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function _getFieldsMap()
    {
        $fields = parent::getFieldsMap();

        foreach ($this->data as $sectionName => $sectionData) {

            foreach ($sectionData as $fieldName) {
                $fields[$sectionName][] = $fieldName;
            }
        }

        return $fields;
    }
}