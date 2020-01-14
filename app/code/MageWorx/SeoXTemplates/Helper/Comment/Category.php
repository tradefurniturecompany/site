<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Helper\Comment;

use MageWorx\SeoXTemplates\Model\Template\Category as CategoryTemplate;

/**
 * SEO XTemplates data helper
 */
class Category extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param CategoryTemplate $type
     * Return comments for category template
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    public function getComments($type)
    {
        $comment = '<br><small>' . $this->getStaticVariableHeader();
        switch ($type) {
            case CategoryTemplate::TYPE_CATEGORY_META_TITLE:
            case CategoryTemplate::TYPE_CATEGORY_META_DESCRIPTION:
            case CategoryTemplate::TYPE_CATEGORY_DESCRIPTION:
            case CategoryTemplate::TYPE_CATEGORY_SEO_NAME:
            case CategoryTemplate::TYPE_CATEGORY_META_KEYWORDS:
                $comment .= '<br><p>' . $this->getCategoryComment();
                $comment .= '<br><p>' . $this->getCategoriesComment();
                $comment .= '<br><p>' . $this->getParentCategoryComment();
                $comment .= '<br><p>' . $this->getParentCategoryByLevelComment();
                $comment .= '<br><p>' . $this->getWebsiteNameComment();
                $comment .= '<br><p>' . $this->getStoreNameComment();
                $comment .= '<br><p>' . $this->getStoreViewNameComment();
                $comment .= '<br><p>' . $this->getDynamicVariableHeader();
                $comment .= '<br><p>' . $this->getLnAllFiltersComment();
                $comment .= '<br><p>' . $this->getLnPersonalFiltersComment();
                $comment .= '<br><p>' . $this->getRandomizeComment();
                break;
            default:
                throw new \UnexpectedValueException(__('SEO XTemplates: Unknow Category Template Type'));
        }
        return $comment.'</small>';
    }

    /**
     * Return Static Variable header
     *
     * @return string
     */
    protected function getStaticVariableHeader()
    {
        return '<p><p><b><u>' . __('Static Template variables:') . '</u></b>' . ' ' .
            __('their values are written in product/category attributes in the backend.') . ' ' .
            __('The values of randomizer feature will also be written in the attibutes.');
    }

    /**
     * Return Dynamic Variable header
     *
     * @return string
     */
    protected function getDynamicVariableHeader()
    {
        return '<br><p><p><b><u>' . __('Dynamic Template variables:') . '</u></b>' .
            ' <font color = "#ea7601">' . __('their values will only be seen on the frontend. In the backend you’ll see the variables themselves.') . '</font>' .
            ' ' . __('Here randomizer values will change with every page refresh.');
    }

    /**
     * Return comment for Category
     *
     * @return string
     */
    protected function getCategoryComment()
    {
        return '<b>[category]</b> - ' . __('output a current category name') . ';';
    }

    /**
     * Return comment for Categories
     *
     * @return string
     */
    protected function getCategoriesComment()
    {
        return '<b>[categories]</b> - ' . __('output a current categories chain starting from the first parent category and ending a current category') . ';';
    }

    /**
     * Return comment for Parent Category
     *
     * @return string
     */
    protected function getParentCategoryComment()
    {
        return '<b>[parent_category]</b> - ' . __('output a parent category name') . ';';
    }

    /**
     * Return comment for Parent Category by level
     *
     * @return string
     */
    protected function getParentCategoryByLevelComment()
    {
        $html = '<b>[parent_category_1]</b> - ' . __('outputs the 1st parent category name. It equals to [parent_category]') . ';';
        $html .= '<br>' . '<b>[parent_category_2]</b> - ' . __('outputs the 2st parent category name') . ';';
        $html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;' . __('etc.');
        $html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;' . __('The orders of the parent categories is as follows: <br>&nbsp;&nbsp;&nbsp;&nbsp; yoursite/parent_category_3/parent_category_2/parent_category_1/category.html') . ';';
        return $html;
    }

    /**
     * Return comment for Subcategories
     *
     * @return string
     */
    protected function getSubcategoriesComment()
    {
        return '<b>[subcategories]</b> - ' . __('output a list of subcategories for a current category') . ';';
    }

    /**
     * Return comment for Website Name
     *
     * @return string
     */
    protected function getWebsiteNameComment()
    {
        return '<b>[website_name]</b> - ' . __('output a current website name') . ';';
    }

    /**
     * Return comment for Store Name
     *
     * @return string
     */
    protected function getStoreNameComment()
    {
        return '<b>[store_name]</b> - ' . __('output a current store name') . ';';
    }

    /**
     * Return comment for Store View Name
     *
     * @return string
     */
    protected function getStoreViewNameComment()
    {
        return '<b>[store_view_name]</b> - ' . __('output a current store view name') . ';';
    }


    /**
     * Return comment for filter_all
     *
     * @return string
     */
    protected function getLnAllFiltersComment()
    {
        $string = '<b>[filter_all]</b> - ' . __('inserts all chosen attributes of LN on the category page.');
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('Example:') . " <b>" . '[category][ – parameters: {filter_all}]' . "</b>";
        $string .= " - " . __('If "color", "occasion", and "shoe size" attributes are chosen, on the frontend you will see:');
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('"Shoes – parameters: Color Red, Occasion Casual, Shoe Size 6.5"');
        $string .= " - " . __('If no attributes are chosen, you will see: "Shoes".');

        return $string;
    }

    /**
     * Return comment for personal filters
     *
     * @return string
     */
    protected function getLnPersonalFiltersComment()
    {
        $string = '<b>[filter_<i>attribute_code</i>]</b> - ' . __('insert attribute value if exists.');
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('Example:') . ' <b>[category][ in {filter_color}]</b>';
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('Will translate to "Shoes in Color Red" on the frontend.');

        $string .= '<br><b>[filter_<i>attribute_code</i>_label]</b> - ' . __('inserts mentioned product attribute label on the category LN page.');
        $string .= '<br><b>[filter_<i>attribute_code</i>_value]</b> - ' . __('inserts mentioned product attribute value on the category LN page.');

        return $string;
    }

    /**
     * Return comment for randomizer
     *
     * @return string
     */
    protected function getRandomizeComment()
    {
        return '<p>'. __('Randomizer feature is available. The construction like [Buy||Order||Purchase] will use a randomly picked word.').'<br>'.__('
        Also randomizers can be used within other template variables, ex: [-parameters:||-filters: {filter_all}]. Number of randomizers blocks is not limited within the template.').'<br>';
    }
}
