<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model;

use MageWorx\SeoXTemplates\Helper\Data as HelperData;
use MageWorx\SeoXTemplates\Helper\Converter as HelperConverter;
use \MageWorx\SeoXTemplates\Model\ResourceModel\Category;

abstract class Converter implements ConverterInterface
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperConverter
     */
    protected $helperConverter;

    /**
     *
     * @var \Magento\Catalog\Model\AbstractModel|null
     */
    protected $item = null;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var boolean
     */
    protected $isDynamically;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category
     */
    protected $resourceCategory;

    /**
     *
     * @param array $vars
     * @param string $templateCode
     * @return string
     */
    abstract protected function __convert($vars, $templateCode);

    /**
     *
     * @param string $templateCode
     */
    abstract protected function stopProccess($templateCode);

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param HelperData $helperData
     * @param HelperConverter $helperConverter
     * @param  Category $categoryResource
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        HelperData $helperData,
        HelperConverter $helperConverter,
        Category $resourceCategory,
        \Magento\Framework\App\Request\Http $request
    ) {
    
        $this->storeManager    = $storeManager;
        $this->helperData = $helperData;
        $this->helperConverter = $helperConverter;
        $this->request = $request;
        $this->resourceCategory = $resourceCategory;
    }

    /**
     * Retrieve converted string from template code
     *
     * @param \Magento\Catalog\Model\AbstractModel $item
     * @param string $templateCode
     * @param boolean $isDynamically
     * @return string
     */
    public function convert($item, $templateCode, $isDynamically = false)
    {
        $this->isDynamically = $isDynamically;
        if ($this->stopProccess($templateCode)) {
            return $templateCode;
        }
        $templateCode = $this->_randomizeUndependentStaticValues($templateCode);

        $this->_setItem($item);
        $vars = $this->parse($templateCode);
        $convertValue = $this->__convert($vars, $templateCode);

        return $convertValue;
    }

    /**
     *
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     *
     * @param \Magento\Catalog\Model\AbstractModel $item
     */
    protected function _setItem($item)
    {
        $this->item = $item;
    }

    /**
     * Retrieve parsed vars from template code
     *
     * @param string $templateCode
     * @return array
     */
    public function parse($templateCode)
    {
        $vars = [];
        preg_match_all('~(\[(.*?)\])~', $templateCode, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            preg_match('~^((?:(.*?)\{(.*?)\}(.*)|[^{}]*))$~', $match[2], $params);
            array_shift($params);

            if (count($params) == 1) {
                $vars[$match[1]]['prefix']     = $vars[$match[1]]['suffix']     = '';
                $vars[$match[1]]['attributes'] = explode('|', $params[0]);
            } else {
                $vars[$match[1]]['prefix']     = $params[1];
                $vars[$match[1]]['suffix']     = $params[3];
                $vars[$match[1]]['attributes'] = explode('|', $params[2]);
            }
        }
        return $vars;
    }

    /**
     * Convert static values, marked as "randomize"
     *
     * [The Best Product||Our bestseller]: [name][manufacturer||brand {manufacturer|brand}]
     * Our bestseller: [name][manufacturer||brand {manufacturer|brand}]
     *
     * @param string $templateCode
     * @return string
     */
    protected function _randomizeUndependentStaticValues($templateCode)
    {
        preg_match_all('~(\[([^\[\{\}]*?\|\|[^\[\{\}]*?)\])~', $templateCode, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (!empty($match[2])) {
                $value = $this->helperConverter->randomize($match[2]);
                $templateCode = str_replace($match[0], $value, $templateCode);
            }
        }

        return $templateCode;
    }

    /**
     * @return array
     */
    protected function _getRequestParams()
    {
        $params = $this->request->getParams();

        return $params;
    }

    /**
     * @param $id
     * @param $attribute
     * @param null $storeId
     * @return mixed
     */
    protected function _getRawCategoryAttributeValue($id, $attribute, $storeId = null)
    {
        $storeId = $storeId === null ? $this->storeManager->getStore()->getId() : $storeId;
        return $this->resourceCategory->getAttributeRawValue($id, $attribute, $storeId);
    }
}
