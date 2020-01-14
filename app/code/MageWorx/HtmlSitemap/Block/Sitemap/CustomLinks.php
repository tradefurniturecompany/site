<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Block\Sitemap;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;

/**
 * Custom links block
 */
class CustomLinks extends Template
{
    /**
     *
     * @var \Magento\Framework\Object
     */
    protected $customLinks;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
    
        $this->customLinks = new \Magento\Framework\DataObject;
        parent::__construct($context, $data);
    }

    /**
     *   You can add custom data using event observer. The data structure shall be such:
     *
     *   $object = $observer->getObject();
     *   $foo = [
     *       'section_title' => 'Blog Links',
     *       'items' => [
     *           0 => ['url' => 'http://mysite.com/my_first_blog_post.html', 'title' => 'My First Blog Post'],
     *           1 => ['url' => 'http://mysite.com/my_second_blog_post.html', 'title' => 'My Second Blog Post']
     *       ]
     *   ];
     *   $object->addData(['Extension_Name' => $foo]);
     */
    public function getCustomLinkContainer()
    {
        $this->_eventManager->dispatch(
            'mageworx_html_sitemap_load_additional_collection',
            ['object' => $this->customLinks]
        );
        return $this->customLinks;
    }
}
