<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\DataProvider\Product;

use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ResourceModel\Product;
use MageWorx\SeoXTemplates\Model\ConverterProductFactory;
use MageWorx\SeoAll\Helper\LinkFieldResolver;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Catalog\Api\Data\ProductInterface;
use MageWorx\SeoXTemplates\Model\ResourceModel\Product\Gallery as GalleryResource;
use MageWorx\SeoXTemplates\Model\Product\Gallery\ReadHandler as GalleryReadHandler;
use Magento\Framework\DB\Select;

class Gallery extends \MageWorx\SeoXTemplates\Model\DataProvider\Product
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @var int
     */
    protected $_defaultStore;

    /**
     * Store ID for obtaining and preparing data
     *
     * @var int
     */
    protected $_storeId;

    /**
     * @var HelperStore
     */
    protected $helperStore;

    /**
     *
     * @var array
     */
    protected $_attributeCodes = [];

    /**
     *
     * @var \Magento\Framework\Data\Collection
     */
    protected $_collection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * @var \MageWorx\SeoAll\Helper\LinkFieldResolver
     */
    protected $linkFieldResolver;

    /**
     * @var \MageWorx\SeoXTemplates\Model\ResourceModel\Product\Gallery
     */
    protected $galleryResource;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Gallery constructor.
     *
     * @param ResourceConnection $resource
     * @param ConverterProductFactory $converterProductFactory
     * @param LinkFieldResolver $linkFieldResolver
     * @param Product $productResource
     * @param HelperStore $helperStore
     * @param GalleryResource $galleryResource
     * @param GalleryReadHandler $galleryReadHandler
     */
    public function __construct(
        ResourceConnection $resource,
        ConverterProductFactory $converterProductFactory,
        LinkFieldResolver $linkFieldResolver,
        Product $productResource,
        HelperStore $helperStore,
        GalleryResource $galleryResource,
        GalleryReadHandler $galleryReadHandler
    ) {
        parent::__construct($resource, $converterProductFactory);
        $this->linkFieldResolver  = $linkFieldResolver;
        $this->productResource    = $productResource;
        $this->helperStore        = $helperStore;
        $this->galleryResource    = $galleryResource;
        $this->galleryReadHandler = $galleryReadHandler;
    }

    /**
     * Retrieve data
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\Template\Product $template
     * @param int|null $customStoreId
     * @return array
     */
    public function getData($collection, $template, $customStoreId = null)
    {
        if (!$collection) {
            return false;
        }

        $this->_collection = $collection;
        $this->_storeId    = $this->getStoreId($template, $customStoreId);

        if ($template->getIsSingleStoreMode()) {
            $this->_storeId = $template->getStoreId();
        }

        $this->_attributeCodes = $template->getAttributeCodesByType();

        $labelKey = $this->_storeId == 0 ? 'label_default' : 'label';

        foreach ($this->_attributeCodes as $attributeCode) {

            if ($attributeCode === 'media_gallery') {

                $attributeData = [];

                /** @var \Magento\Catalog\Model\Product $product */
                foreach ($collection as $product) {
                    $mediaGallery = $product->getData('media_gallery');

                    if (empty($mediaGallery['images'])) {
                        continue;
                    }

                    foreach ($mediaGallery['images'] as $imageData) {

                        if ($imageData[$labelKey] && $template->isScopeForEmpty()) {
                            continue;
                        }

                        $position = $this->getImagePosition($imageData);
                        $disabled = $this->getImageDisabled($imageData);

                        $converter      = $this->converterProductFactory->create($attributeCode);
                        $attributeValue = $converter->convert(
                            $product
                                ->setStoreId($this->_storeId)
                                ->setData('current_image_position', $position),
                            $template->getCode()
                        );

                        $linkField = $this->getLinkField();

                        $attributeData[$product->getId()][$imageData['value_id']] = [
                            $linkField  => $imageData[$linkField],
                            'value_id'  => $imageData['value_id'],
                            'store_id'  => $this->_storeId,
                            'label'     => $attributeValue,
                            'old_label' => $imageData[$labelKey],
                            'file'      => $imageData['file'],
                            'position'  => $position,
                            'disabled'  => $disabled
                        ];
                    }
                }

                $data[$attributeCode] = $attributeData;
            }
        }

        return $data;
    }

    /**
     * @param array $imageData
     * @return string
     */
    protected function getImagePosition(array $imageData)
    {
        if ((int)$this->_storeId) {
            $position = ($imageData['position'] === null) ? $imageData['position_default'] : $imageData['position'];
        } else {
            $position = $imageData['position_default'];
        }

        return $position;
    }


    /**
     * @param array $imageData
     * @return int
     */
    protected function getImageDisabled(array $imageData)
    {
        if ((int)$this->_storeId) {
            $position = ($imageData['disabled'] === null) ? $imageData['disabled_default'] : $imageData['disabled'];
        } else {
            $position = $imageData['disabled_default'];
        }

        return (int)$position;
    }

    /**
     * You can load collection and add specific data to items here
     *
     * @param \MageWorx\SeoXTemplates\Model\Template\Product $template
     * @param \Magento\Framework\Data\Collection\AbstractDb $collection Non-loaded collection
     * @return mixed|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function onLoadEntityCollection($template, $collection)
    {
        $attributes = $template->getAttributeCodesByType();

        $collection->setStoreId($template->getStoreId());

        foreach ($attributes as $attributeCode) {

            if ($attributeCode == 'media_gallery') {

                if ($template->isScopeForEmpty()) {
                    $this->galleryResource->addEmptyLabelFilter($collection);
                }

                $this->addMediaGalleryData($collection);
            }
        }

        return parent::onLoadEntityCollection($template, $collection);
    }

    /**
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param int|null $customStoreId
     * @return int|null
     */
    protected function getStoreId(
        $template,
        $customStoreId = null
    ) {
        if ($customStoreId) {
            return $customStoreId;
        }

        if ($template->getIsSingleStoreMode()) {
            return $this->helperStore->getCurrentStoreId();
        }

        return $template->getStoreId();
    }

    /**
     * @return array
     */
    public function getAttributeCodes()
    {
        return $this->_attributeCodes;
    }

    /**
     * @return string
     */
    protected function getLinkField()
    {
        return $this->linkFieldResolver->getLinkField(ProductInterface::class, 'entity_id');
    }

    /**
     * Add media gallery data to loaded items
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return $this
     */
    protected function addMediaGalleryData($collection)
    {
        if ($collection->getFlag('media_gallery_added')) {
            return $collection;
        }

        if (!$collection->count()) {
            return $collection;
        }

        $items     = $collection->getItems();
        $linkField = $this->getLinkField();


        /** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $attribute = $collection->getAttribute('media_gallery');

//        $select    = $this->galleryResource->createBatchBaseSelect(
        $select = $this->galleryResource->createImageBatchBaseSelect(
            $collection->getStoreId(),
            $attribute->getAttributeId()
        )->reset(
            Select::ORDER
        )->where(
            'entity.' . $linkField . ' IN (?)',
            array_map(
                function ($item) use ($linkField) {
                    return (int)$item->getOrigData($linkField);
                },
                $items
            )
        );

        $mediaGalleries = [];

        foreach ($collection->getConnection()->fetchAll($select) as $row) {
            $mediaGalleries[$row[$linkField]][] = $row;
        }

        foreach ($items as $item) {

            if (isset($mediaGalleries[$item->getOrigData($linkField)])) {
                $mediaEntries = $mediaGalleries[$item->getOrigData($linkField)];
            } else {
                $mediaEntries = [];
            }

            $this->galleryReadHandler->addMediaDataToProduct($item, $mediaEntries);
        }

        $collection->setFlag('media_gallery_added', true);

        return $this;
    }
}
