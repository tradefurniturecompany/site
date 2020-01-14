<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Helper\Comment;

use MageWorx\SeoXTemplates\Model\Template\LandingPage as LandingPageTemplate;

/**
 * SEO XTemplates data helper
 */
class LandingPage extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param LandingPageTemplate $type
     * Return comments for Landing Page template
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    public function getComments($type)
    {
        $comment = '<br><small>' . $this->getStaticVariableHeader();
        switch ($type) {
            case LandingPageTemplate::TYPE_LANDING_PAGE_META_TITLE:
            case LandingPageTemplate::TYPE_LANDING_PAGE_META_DESCRIPTION:
            case LandingPageTemplate::TYPE_LANDING_PAGE_META_KEYWORDS:
            case LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_1:
            case LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_2:
            case LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_3:
            case LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_4:
                $comment .= '<br><p>' . $this->getLandingPageComment();
                $comment .= '<br><p>' . $this->getWebsiteNameComment();
                $comment .= '<br><p>' . $this->getStoreNameComment();
                $comment .= '<br><p>' . $this->getStoreViewNameComment();
                $comment .= '<br><p>' . $this->getDynamicVariableHeader();
                $comment .= '<br><p>' . $this->getLnAllFiltersComment();
                $comment .= '<br><p>' . $this->getLnPersonalFiltersComment();
                $comment .= '<br><p>' . $this->getRandomizeComment();
                break;
            case LandingPageTemplate::TYPE_LANDING_PAGE_URL_KEY:
                $comment .= $this->getUrlExample();
            case LandingPageTemplate::TYPE_LANDING_PAGE_HEADER:
                $comment .= '<br><p>' . $this->getLandingPageComment();
                $comment .= '<br><p>' . $this->getWebsiteNameComment();
                $comment .= '<br><p>' . $this->getStoreNameComment();
                $comment .= '<br><p>' . $this->getStoreViewNameComment();
                $comment .= '<br><p>' . $this->getRandomizeComment();
                break;
            default:
                throw new \UnexpectedValueException(__('SEO XTemplates: Unknow Landing Page Template Type'));
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
            __('their values are written in landing page attributes in the backend.') . ' ' .
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
     * Return comment for Landing Page
     *
     * @return string
     */
    protected function getLandingPageComment()
    {
        return '<b>[landing_page]</b> - ' . __('output a landing page title') . ';<br>' .
            '<b>[meta_title]</b> - ' . __('output a landing page meta title') . ';<br>' .
            '<b>[meta_description]</b> - ' . __('output a landing page meta description') . ';<br>' .
            '<b>[meta_keywords]</b> - ' . __('output a landing page meta keywords') . ';<br>' .
            '<b>[header]</b> - ' . __('output a landing page header') . ';<br>' .
            '<b>[text_1]</b> - ' . __('output a landing page text variable 1') . ';<br>' .
            '<b>[text_2]</b> - ' . __('output a landing page text variable 2') . ';<br>' .
            '<b>[text_3]</b> - ' . __('output a landing page text variable 3') . ';<br>' .
            '<b>[text_4]</b> - ' . __('output a landing page text variable 4') . ';';
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
        $string = '<b>[filter_all]</b> - ' . __('inserts all chosen attributes of LN on the landing page.');
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('Example:') . " <b>" . '[landing_page][ – parameters: {filter_all}]' . "</b>";
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
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('Example:') . ' <b>[landing_page][ in {filter_color}]</b>';
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('Will translate to "Shoes in Color Red" on the frontend.');

        $string .= '<br><b>[filter_<i>attribute_code</i>_label]</b> - ' . __('inserts mentioned product attribute label on the landing page LN page.');
        $string .= '<br><b>[filter_<i>attribute_code</i>_value]</b> - ' . __('inserts mentioned product attribute value on the landing page LN page.');

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
        Also randomizers can be used within other template variables, ex: [Name:||Title: {landing_page}]. Number of randomizers blocks is not limited within the template.').'<br>';
    }

    /**
     * @return string
     */
    protected function getUrlExample()
    {
        return '<p><b>' . __('Example') . '</b><p>[landing_page][ for {store|website}] [store_name] <p>' . __('will be transformed into') .
            '<br><p>red-tops-for-default-store<br>';
    }
}
