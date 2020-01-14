<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\DynamicRenderer;

class Category
{
    /**
     * @var string
     */
    protected $isConvertedTitle;

    /**
     * @var string
     */
    protected $isConvertedSeoName;

    /**
     * @var string
     */
    protected $isConvertedMetaDescription;

    /**
     * @var string
     */
    protected $isConvertedMetaKeywords;

    /**
     * @var string
     */
    protected $isConvertedDescription;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Category\MetaTitle
     */
    protected $metaTitleConverter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Category\MetaDescription
     */
    protected $metaDescriptionConverter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Category\MetaKeywords
     */
    protected $metaKeywordsConverter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Category\SeoName
     */
    protected $seoNameConverter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Category\Description
     */
    protected $descriptionConverter;

    /**
     * @var \Zend\Filter\StripTags
     */
    protected $stripTags;
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * Category constructor.
     *
     * @param \MageWorx\SeoXTemplates\Model\Converter\Category\MetaTitle $metaTitleConverter
     * @param \MageWorx\SeoXTemplates\Model\Converter\Category\MetaDescription $metaDescriptionConverter
     * @param \MageWorx\SeoXTemplates\Model\Converter\Category\MetaKeywords $metaKeywordsConverter
     * @param \MageWorx\SeoXTemplates\Model\Converter\Category\Description $descriptionConverter
     * @param \MageWorx\SeoXTemplates\Model\Converter\Category\SeoName $seoNameConverter
     * @param \Zend\Filter\StripTags $stripTags
     * @param \Magento\Framework\View\Page\Config $pageConfig
     */
    public function __construct(
        \MageWorx\SeoXTemplates\Model\Converter\Category\MetaTitle $metaTitleConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Category\MetaDescription $metaDescriptionConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Category\MetaKeywords $metaKeywordsConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Category\Description $descriptionConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Category\SeoName $seoNameConverter,
        \Zend\Filter\StripTags $stripTags,
        \Magento\Framework\View\Page\Config $pageConfig
    ) {
        $this->metaTitleConverter       = $metaTitleConverter;
        $this->metaDescriptionConverter = $metaDescriptionConverter;
        $this->metaKeywordsConverter    = $metaKeywordsConverter;
        $this->descriptionConverter     = $descriptionConverter;
        $this->seoNameConverter         = $seoNameConverter;
        $this->stripTags                = $stripTags;
        $this->pageConfig               = $pageConfig;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return boolean
     */
    public function modifyCategoryTitle($category)
    {
        if ($this->isConvertedTitle) {
            return true;
        }

        $this->isConvertedTitle = true;
        $title                  = $this->metaTitleConverter->convert($category, $category->getMetaTitle(), true);

        if (!empty($title)) {
            $title = trim(htmlspecialchars(html_entity_decode($title, ENT_QUOTES, 'UTF-8')));
            if ($title) {
                $this->pageConfig->getTitle()->set($title);

                return true;
            }
        };

        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return boolean
     */
    public function modifyCategoryMetaDescription($category)
    {
        if ($this->isConvertedMetaDescription) {
            return true;
        }

        $metaDescription = $this->metaDescriptionConverter->convert($category, $category->getMetaDescription(), true);
        if (!empty($metaDescription)) {
            $metaDescription = htmlspecialchars(
                html_entity_decode(
                    preg_replace(
                        ['/\r?\n/', '/[ ]{2,}/'],
                        [' ', ' '],
                        $this->stripTags->filter($metaDescription)
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
            if ($metaDescription) {
                $this->isConvertedMetaDescription = $metaDescription;
                $this->pageConfig->setDescription($metaDescription);

                return true;
            }
        }

        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return boolean
     */
    public function modifyCategoryMetaKeywords($category)
    {
        if ($this->isConvertedMetaKeywords) {
            return true;
        }

        $metaKeywords = $this->metaKeywordsConverter->convert($category, $category->getMetaKeywords(), true);
        if (!empty($metaKeywords)) {
            $metaKeywords = htmlspecialchars(
                html_entity_decode(
                    preg_replace(
                        ['/\r?\n/', '/[ ]{2,}/'],
                        [' ', ' '],
                        $this->stripTags->filter($metaKeywords)
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
            if ($metaKeywords) {
                $this->isConvertedMetaKeywords = $metaKeywords;
                $this->pageConfig->setKeywords($metaKeywords);

                return true;
            }
        }

        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return boolean
     */
    public function modifyCategoryDescription($category)
    {
        if ($this->isConvertedDescription) {
            return true;
        }
        $description = $this->descriptionConverter->convert($category, $category->getDescription(), true);
        if (!empty($description)) {
            $this->isConvertedDescription = $description;
            $category->setDescription($description);

            return true;
        }

        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param string $seoName
     * @return string
     */
    public function getModifiedCategorySeoName($category, $seoName)
    {
        if ($this->isConvertedSeoName) {
            return $category->getCategorySeoName();
        }
        $seoName = $this->seoNameConverter->convert($category, $seoName, true);
        if (!empty($seoName)) {
            $this->isConvertedSeoName = $seoName;
            $category->setCategorySeoName($seoName);

            return $seoName;
        }

        return $seoName;
    }
}
