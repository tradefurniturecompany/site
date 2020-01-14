<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\DbWriter\Product;

use Magento\Framework\App\ResourceConnection;
use MageWorx\SeoXTemplates\Model\DataProviderProductFactory;
use MageWorx\SeoAll\Helper\LinkFieldResolver;
use Magento\Catalog\Model\ResourceModel\Product\Gallery as GalleryResource;
use Magento\Catalog\Api\Data\ProductInterface;

class Gallery extends \MageWorx\SeoXTemplates\Model\DbWriter\Product
{
    /**
     * @var \MageWorx\SeoXTemplates\Model\DataProviderInterface
     */
    protected $dataProvider;

    /**
     * @var \MageWorx\SeoAll\Helper\LinkFieldResolver
     */
    protected $linkFieldResolver;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_collection;

    /**
     * @var GalleryResource
     */
    protected $galleryResource;

    /**
     * Gallery constructor.
     *
     * @param ResourceConnection $resource
     * @param DataProviderProductFactory $dataProviderProductFactory
     * @param LinkFieldResolver $linkFieldResolver
     * @param GalleryResource $galleryResource
     */
    public function __construct(
        ResourceConnection $resource,
        DataProviderProductFactory $dataProviderProductFactory,
        LinkFieldResolver $linkFieldResolver,
        GalleryResource $galleryResource
    ) {
        parent::__construct($resource, $dataProviderProductFactory);
        $this->linkFieldResolver = $linkFieldResolver;
        $this->galleryResource   = $galleryResource;
    }

    /**
     * Write to database converted string from template code
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param int $customStoreId
     * @return array|false
     */

    public function write($collection, $template, $customStoreId = null)
    {
        if (!$collection) {
            return false;
        }

        $this->_collection  = $collection;
        $this->dataProvider = $this->dataProviderProductFactory->create($template->getTypeId());
        $data               = $this->dataProvider->getData($collection, $template, $customStoreId);

        foreach ($data as $attributeCode => $attributeData) {
            $this->attributeDataWrite($attributeCode, $attributeData);
        }

        return true;
    }

    /**
     * Write dispatch
     *
     * @param string $attributeCode
     * @param array $attributeData
     */
    protected function attributeDataWrite($attributeCode, $attributeData)
    {
        if ($attributeCode === 'media_gallery') {

            foreach ($attributeData as $productId => $multipleData) {

                foreach ($multipleData as $valueId => $data) {
                    $this->galleryResource->deleteGalleryValueInStore(
                        $data['value_id'],
                        $data[$this->linkFieldResolver->getLinkField(ProductInterface::class, 'entity_id')],
                        $data['store_id']
                    );
                    $this->galleryResource->insertGalleryValueInStore($data);
                }
            }
        }
    }
}