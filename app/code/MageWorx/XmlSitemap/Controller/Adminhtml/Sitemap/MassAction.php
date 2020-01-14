<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Controller\Adminhtml\Sitemap;

use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use MageWorx\XmlSitemap\Model\ResourceModel\Sitemap\CollectionFactory;
use MageWorx\XmlSitemap\Model\SitemapFactory as SitemapFactory;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\XmlSitemap\Controller\Adminhtml\Sitemap as SitemapController;
use MageWorx\XmlSitemap\Model\Sitemap as SitemapModel;

abstract class MassAction extends SitemapController
{
    /**
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var string
     */
    protected $successMessage = 'Mass Action successful on %1 records';
    /**
     * @var string
     */
    protected $errorMessage = 'Mass Action failed';

    /**
     * MassAction constructor.
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param Registry $registry
     * @param SitemapFactory $sitemapFactory
     * @param Context $context
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Filter $filter,
        Registry $registry,
        SitemapFactory $sitemapFactory,
        Context $context
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        parent::__construct($registry, $sitemapFactory, $context);
    }

    /**
     *
     * @param SitemapModel $sitemap
     * @return mixed
     */
    abstract protected function doTheAction(SitemapModel $sitemap);

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $sitemap) {
                $this->doTheAction($sitemap);
            }
            $this->messageManager->addSuccess(__($this->successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __($this->errorMessage));
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('mageworx_xmlsitemap/*/index');
        return $redirectResult;
    }
}
