<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Controller\Adminhtml\Sitemap;

use MageWorx\XmlSitemap\Model\Sitemap as SitemapModel;


class MassGenerate extends MassAction
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 sitemap(s) have been generated';

    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while generating sitemap(s).';

    /**
     * @param SitemapModel $sitemap
     * @return $this
     */
    protected function doTheAction(SitemapModel $sitemap)
    {
        $sitemap->generateXml();
        return $this;
    }
}
