<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Controller\Adminhtml\Sitemap;

use Magento\Framework\Registry;
use MageWorx\XmlSitemap\Controller\Adminhtml\Sitemap as SitemapController;
use MageWorx\XmlSitemap\Model\SitemapFactory as SitemapFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Backend\App\Action\Context;
use MageWorx\XmlSitemap\Model\Source\EntityType;

class Save extends SitemapController
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * Save constructor.
     * @param DateTime $date
     * @param JsHelper $jsHelper
     * @param Registry $registry
     * @param SitemapFactory $sitemapFactory
     * @param Context $context
     */
    public function __construct(
        DateTime $date,
        JsHelper $jsHelper,
        Registry $registry,
        SitemapFactory $sitemapFactory,
        Context $context
    ) {

        $this->date     = $date;
        $this->jsHelper = $jsHelper;
        parent::__construct($registry, $sitemapFactory, $context);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data['sitemap_path'] = $this->getRequest()->getPost('sitemap_path');
        $data['store_id']     = $this->getRequest()->getPost('store_id');

        $sitemapFileName = $this->getRequest()->getPost('sitemap_filename');
        $entityTypes     = $this->getRequest()->getPost('entity_type', [EntityType::DEFAULT_TYPE]);

        $resultRedirect = $this->resultRedirectFactory->create();

        if ( $data['sitemap_path'] && $sitemapFileName && $data['store_id']) {

            foreach ($entityTypes as $entityType) {

                $this->coreRegistry->unregister('mageworx_xmlsitemap_sitemap');

                $data['sitemap_filename'] = $sitemapFileName;
                $data['entity_type']      = $entityType;

                if (!empty($this->getRequest()->getParam('sitemap_id'))) {
                    $data['sitemap_id'] = $this->getRequest()->getParam('sitemap_id');

                    $sitemap = $this->initSitemap($data['sitemap_id']);

                } else {
                    $sitemap = $this->initSitemap();

                    $data['sitemap_filename'] = $this->getPreparedFileName($sitemapFileName, $entityType);
                }

                $sitemap->setData($data);

                $this->_eventManager->dispatch(
                    'mageworx_xmlsitemaps_sitemap_prepare_save',
                    [
                        'sitemap' => $sitemap,
                        'request' => $this->getRequest()
                    ]
                );

                try {
                    $sitemap->save();
                    $this->_getSession()->setMageworxXmlSitemapData(false);
                    $this->messageManager->addSuccessMessage(
                        __('Sitemap %1 was successfully saved', $sitemap->getSitemapFilename())
                    );

                    if ($this->getRequest()->getParam('generate')) {
                        $sitemap->generateXml();
                    }

                    continue;

                } catch (LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the sitemap.'));
                }

                $this->_getSession()->setMageworxXmlSitemapData($data);
                $resultRedirect->setPath(
                    'mageworx_xmlsitemap/*/edit',
                    [
                        'sitemap_id' => $sitemap->getSitemapId(),
                        '_current' => true
                    ]
                );
                return $resultRedirect;
            }
        }

        $resultRedirect->setPath('mageworx_xmlsitemap/*/');
        return $resultRedirect;
    }

    protected function getPreparedFileName($filename, $entityType)
    {
        if ($entityType == EntityType::DEFAULT_TYPE) {
            return $filename;
        }

        if (preg_match('#\.xml$#', $filename)) {
            $filename = str_replace('.xml', '', $filename);
        }

        return $filename . "_" . $entityType;
    }
}
