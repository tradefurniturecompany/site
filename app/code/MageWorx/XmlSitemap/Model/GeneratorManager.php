<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model;

use MageWorx\XmlSitemap\Helper\Data as Helper;
use MageWorx\XmlSitemap\Model\Source\EntityType;
use MageWorx\XmlSitemap\Model\Generator\Product as GeneratorProduct;

/**
 * {@inheritdoc}
 */
class GeneratorManager
{
    /**
     * @var array
     */
    protected $countByEntity;

    /**
     * @var Sitemap
     */
    protected $model;

    /**
     * @var
     */
    protected $helper;

    /**
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * @var GeneratorFactory
     */
    protected $generatorFactory;

    /**
     * @var GeneratorProduct
     */
    protected $generatorProduct;

    /**
     * @var Writer
     */
    protected $xmlWriter;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * GeneratorManager constructor.
     * @param GeneratorFactory $generatorFactory
     * @param Helper $helper
     * @param WriterInterface $xmlWriter
     * @param GeneratorProduct $generatorProduct
     */
    public function __construct(
        GeneratorFactory $generatorFactory,
        Helper $helper,
        WriterInterface $xmlWriter,
        GeneratorProduct $generatorProduct
    ) {
        $this->generatorProduct = $generatorProduct;
        $this->generatorFactory = $generatorFactory;
        $this->helper           = $helper;
        $this->xmlWriter        = $xmlWriter;
    }

    /**
     * @param Sitemap $model
     */
    protected function init(Sitemap $model)
    {
        $this->model               = $model;
        $this->storeId             = $model->getStoreId();
        $this->helper->init($this->storeId);

        $this->xmlWriter->init(
            $this->model->getFullPath(), $this->model->getSitemapPath(), $this->model->getSitemapFilename(),
            $this->model->getFullTempPath(), $this->model->getStoreBaseUrl(), $this->storeId
        );
    }

    /**
     * @param Sitemap $sitemap
     */
    public function generateXml(Sitemap $sitemap)
    {
        $this->init($sitemap);
        $this->xmlWriter->startWriteXml();

        $sitemapEntityType = $sitemap->getEntityType();

        foreach ($this->generatorFactory->getAllGenerators() as $generatorCode => $model) {

            if (!$this->isSuitableGenerator($sitemapEntityType, $generatorCode)) {
                continue;
            }

            $this->generator = $model;

            $this->generator->generate($this->storeId, $this->xmlWriter);
            $this->countByEntity[$generatorCode] = [
                'title' => $this->generator->getName(),
                'value' => $this->generator->getCounter()
            ];
        }

        if (
            $sitemapEntityType == EntityType::DEFAULT_TYPE
            || $sitemapEntityType == $this->generatorProduct->getCode()
        ) {
            $this->countByEntity['images']['title'] = __('Images');
            $this->countByEntity['images']['value'] = $this->xmlWriter->getWritedImages();
        }

        $this->xmlWriter->endWriteXml();
        return;
    }

    /**
     * @return array
     */
    public function getCountByEntity() {
        return $this->countByEntity;
    }

    /**
     * Reset the number of links in sitemap by entity type
     */
    public function resetCountByEntity() {
        $this->countByEntity = [];
    }

    /**
     * @param $sitemapEntityType
     * @param $generatorCode
     * @return bool
     */
    protected function isSuitableGenerator($sitemapEntityType, $generatorCode)
    {
        if ($sitemapEntityType == EntityType::DEFAULT_TYPE ) {
            return true;
        }

        if ($sitemapEntityType == $generatorCode) {
            return true;
        }

        if (
            $sitemapEntityType == EntityType::ADDITIONAL_LINK_TYPE
            && $generatorCode == EntityType::GENERATORS_BY_OBSERVER_TYPE
        ) {
            return true;
        }

        return false;
    }
}
