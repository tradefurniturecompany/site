<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Controller\Adminhtml\Sitemap;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use MageWorx\XmlSitemap\Model\ResourceModel\Sitemap\CollectionFactory;
use MageWorx\XmlSitemap\Model\SitemapFactory as SitemapFactory;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\XmlSitemap\Model\Sitemap as SitemapModel;



class MassDelete extends MassAction
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 record(s) have been deleted';

    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while deleting record(s).';

    /**
     * @param SitemapModel $sitemap
     * @return $this
     */
    protected function doTheAction(SitemapModel $sitemap)
    {
        $sitemap->delete();
        return $this;
    }
}
